"""
Procesamiento de audio y video del video promocional de Artesanías Mayas (H07).

Hace, con Python + FFmpeg:
  1. RECORTE      : corta cada clip entre dos tiempos (-ss / -t).
  2. VOZ          : genera la narración con la voz sintética de Windows (SAPI).
  3. SUBTÍTULOS   : crea un archivo .srt con los mismos textos de la narración
                    y lo incrusta en el video.
  4. NORMALIZACIÓN: iguala el volumen del audio (filtro loudnorm, EBU R128).
  5. COMPRESIÓN   : exporta el video final en 1080p, 720p y 480p (H.264 + AAC).

Uso (desde la carpeta procesamiento-av):
    python procesar.py
    python procesar.py --media D:\\otra\\carpeta
"""
import argparse
import re
import shutil
import subprocess
import sys
from pathlib import Path

# --------------------------------------------------------------------------
# CONFIGURACIÓN (esto es lo único que normalmente se edita)
# --------------------------------------------------------------------------
# (nombre, archivo, inicio_s, fin_s, texto de la narración)
# fin_s = None significa "hasta el final del archivo".
# texto = None significa "usar el audio original del clip" en lugar de la voz sintética.
ESCENAS = [
    ("intro",    "animacion.mp4",   0.0,  None, "Artesanías Mayas. Piezas hechas a mano, ahora en tu pantalla."),
    ("catalogo", "02_catalogo.mp4", 3.0,  13.0, "Explora el catálogo, filtra por categoría y precio, y gira cada pieza en 3D para verla desde todos los ángulos."),
    ("ar",       "03_ar.mp4",       3.0,  15.0, "Con realidad aumentada, coloca la pieza en tu espacio y comprueba cómo se ve en tu casa."),
    ("vr",       "04_vr.mp4",       4.0,  28.0, "Recorre el taller en realidad virtual y toca los puntos dorados para conocer cada pieza."),
    ("checkout", "05_checkout.mp4", 5.0,  32.0, "Agrega tus piezas al carrito y completa tu compra en pocos pasos."),
    ("admin",    "06_admin.mp4",    3.0,  15.0, "Y el taller gestiona cada pedido desde su propio panel."),
    ("cierre",   "animacion.mp4",   2.25, None, "Artesanías Mayas. Tradición hecha a mano."),
]

# Escenas en las que se conserva el audio original del clip: mientras habla la
# narración sintética el audio del clip baja a VOLUMEN_BAJO y después vuelve a 1.
MEZCLAR = {"vr"}
VOLUMEN_BAJO = 0.25

VOZ = "Microsoft Sabina Desktop"          # voz es-MX instalada en Windows
PRONUNCIACION = {"3D": "tres de"}          # cómo se LEE en voz alta lo que se ESCRIBE en subtítulos
ANCHO, ALTO, FPS = 1920, 1080, 30
COLOR_FONDO = "0xF3E9D8"                   # crema, para las barras de los clips verticales
SALIDAS = [(1080, 18), (720, 21), (480, 24)]   # (alto en px, CRF: menor = mejor calidad)
LOUDNORM = "loudnorm=I=-16:TP=-1.5:LRA=11"
DURACION_MIN, DURACION_MAX = 60, 90        # lo que pide la guía del proyecto


# --------------------------------------------------------------------------
# Utilidades
# --------------------------------------------------------------------------
def ejecutar(cmd, cwd=None, capturar=False):
    """Corre un comando y detiene el programa si falla."""
    r = subprocess.run(cmd, cwd=cwd, capture_output=capturar, text=True,
                       encoding="utf-8", errors="replace")
    if r.returncode != 0:
        print("\nERROR al ejecutar:", " ".join(str(c) for c in cmd))
        if capturar:
            print(r.stderr[-1500:])
        sys.exit(1)
    return r


def duracion(archivo):
    """Duración de un archivo de audio/video en segundos (ffprobe)."""
    r = ejecutar(["ffprobe", "-v", "error", "-show_entries", "format=duration",
                  "-of", "default=nw=1:nk=1", str(archivo)], capturar=True)
    return float(r.stdout.strip())


def tiene_audio(archivo):
    r = ejecutar(["ffprobe", "-v", "error", "-select_streams", "a", "-show_entries",
                  "stream=codec_type", "-of", "csv=p=0", str(archivo)], capturar=True)
    return "audio" in r.stdout


def volumen_lufs(archivo):
    """Volumen integrado (LUFS) de un audio, medido con ebur128."""
    r = ejecutar(["ffmpeg", "-nostats", "-i", str(archivo), "-af", "ebur128=peak=true",
                  "-f", "null", "-"], capturar=True)
    valores = re.findall(r"I:\s+(-?\d+\.?\d*)\s+LUFS", r.stderr)
    return float(valores[-1]) if valores else float("nan")


def tiempo_srt(s):
    ms = int(round(s * 1000))
    h, ms = divmod(ms, 3600000)
    m, ms = divmod(ms, 60000)
    seg, ms = divmod(ms, 1000)
    return f"{h:02d}:{m:02d}:{seg:02d},{ms:03d}"


# --------------------------------------------------------------------------
# 1. RECORTE
# --------------------------------------------------------------------------
def recortar(origen, inicio, fin, destino, trabajo):
    """Recorta [inicio, fin] y deja el clip en 1920x1080, 30 fps, sin audio."""
    total = duracion(origen)
    fin = total if fin is None else min(fin, total)   # no pasar del final real del clip
    dur = fin - inicio
    filtro = (f"scale={ANCHO}:{ALTO}:force_original_aspect_ratio=decrease,"
              f"pad={ANCHO}:{ALTO}:(ow-iw)/2:(oh-ih)/2:color={COLOR_FONDO},"
              f"setsar=1,fps={FPS},format=yuv420p")
    ejecutar(["ffmpeg", "-y", "-v", "error", "-ss", f"{inicio}", "-i", str(origen),
              "-t", f"{dur}", "-an", "-vf", filtro,
              "-c:v", "libx264", "-preset", "veryfast", "-crf", "18", str(destino)], cwd=trabajo)
    return dur


# --------------------------------------------------------------------------
# 2. VOZ (síntesis de voz de Windows, sin instalar nada)
# --------------------------------------------------------------------------
def generar_voz(texto, wav, trabajo):
    if sys.platform != "win32":
        sys.exit("La voz sintética usa Windows SAPI: ejecuta el programa en Windows.")
    for escrito, leido in PRONUNCIACION.items():
        texto = texto.replace(escrito, leido)
    txt = Path(wav).with_suffix(".txt")
    txt.write_text(texto, encoding="utf-8")
    ps = ("Add-Type -AssemblyName System.Speech;"
          "$s = New-Object System.Speech.Synthesis.SpeechSynthesizer;"
          f"$s.SelectVoice('{VOZ}');"
          f"$s.SetOutputToWaveFile('{wav}');"
          f"$s.Speak((Get-Content -Raw -Encoding UTF8 '{txt}'));"
          "$s.Dispose()")
    ejecutar(["powershell", "-NoProfile", "-Command", ps], cwd=trabajo)


# --------------------------------------------------------------------------
# 3. SUBTÍTULOS (archivo .srt)
# --------------------------------------------------------------------------
def escribir_srt(bloques, destino):
    """bloques = [(inicio_s, fin_s, texto)]"""
    lineas = []
    for n, (ini, fin, texto) in enumerate(bloques, 1):
        lineas += [str(n), f"{tiempo_srt(ini)} --> {tiempo_srt(fin)}", texto, ""]
    Path(destino).write_text("\n".join(lineas), encoding="utf-8")


# --------------------------------------------------------------------------
# 4. NORMALIZACIÓN DE AUDIO
# --------------------------------------------------------------------------
def normalizar(entrada, salida, trabajo):
    ejecutar(["ffmpeg", "-y", "-v", "error", "-i", str(entrada), "-af", LOUDNORM,
              "-ar", "44100", "-ac", "2", str(salida)], cwd=trabajo)


# --------------------------------------------------------------------------
# 5. COMPRESIÓN EN VARIAS RESOLUCIONES (+ subtítulos incrustados)
# --------------------------------------------------------------------------
def exportar(video, audio, srt, alto, crf, destino, trabajo):
    estilo = "FontName=Arial,FontSize=11,Outline=2,Shadow=0,MarginV=35"
    filtro = f"subtitles={srt}:force_style='{estilo}',scale=-2:{alto}"
    ejecutar(["ffmpeg", "-y", "-v", "error", "-i", str(video), "-i", str(audio),
              "-vf", filtro, "-c:v", "libx264", "-preset", "medium", "-crf", str(crf),
              "-pix_fmt", "yuv420p", "-c:a", "aac", "-b:a", "128k",
              "-movflags", "+faststart", "-shortest", str(destino)], cwd=trabajo)


# --------------------------------------------------------------------------
def main():
    ap = argparse.ArgumentParser(description="Procesa el video promocional.")
    ap.add_argument("--media", type=Path, default=Path.home() / "multimedia",
                    help="carpeta con animacion.mp4 y la subcarpeta crudos/")
    args = ap.parse_args()

    for herramienta in ("ffmpeg", "ffprobe"):
        if not shutil.which(herramienta):
            sys.exit(f"No se encontró {herramienta} en el PATH.")

    media = args.media.resolve()
    crudos = media / "crudos"
    trabajo = media / "trabajo"
    final = media / "final"
    trabajo.mkdir(parents=True, exist_ok=True)
    final.mkdir(parents=True, exist_ok=True)

    print("== 1) Recorte y 2) voz, escena por escena ==")
    lista_video, lista_audio, bloques = [], [], []
    inicio_escena = 0.0
    for i, (nombre, archivo, ini, fin, texto) in enumerate(ESCENAS):
        origen = media / archivo if archivo == "animacion.mp4" else crudos / archivo
        if not origen.exists():
            sys.exit(f"Falta el archivo: {origen}")

        seg = trabajo / f"seg_{i:02d}_{nombre}.mp4"
        dur = recortar(origen, ini, fin, seg, trabajo)

        aud = trabajo / f"aud_{i:02d}_{nombre}.wav"
        if texto is None:
            # audio original del clip, recortado igual que el video
            if tiene_audio(origen):
                ejecutar(["ffmpeg", "-y", "-v", "error", "-ss", f"{ini}", "-i", str(origen),
                          "-t", f"{dur}", "-vn", "-af", "apad", "-t", f"{dur}",
                          "-ar", "44100", "-ac", "2", str(aud)], cwd=trabajo)
                print(f"  {nombre:9s} video {dur:5.1f} s | audio original del clip")
            else:
                ejecutar(["ffmpeg", "-y", "-v", "error", "-f", "lavfi", "-i",
                          "anullsrc=r=44100:cl=stereo", "-t", f"{dur}", str(aud)], cwd=trabajo)
                print(f"  {nombre:9s} video {dur:5.1f} s | AVISO: el clip no tiene audio (queda en silencio)")
        else:
            wav = trabajo / f"voz_{i:02d}_{nombre}.wav"
            generar_voz(texto, wav, trabajo)
            dur_voz = duracion(wav)
            if nombre in MEZCLAR and tiene_audio(origen):
                # audio original del clip (bajo mientras habla la narración) + narración
                grafo = (f"[0:a]volume='if(lt(t,{dur_voz + 0.3}),{VOLUMEN_BAJO},1)':eval=frame[a0];"
                         "[1:a]apad[a1];[a0][a1]amix=inputs=2:duration=first:normalize=0[m]")
                ejecutar(["ffmpeg", "-y", "-v", "error", "-ss", f"{ini}", "-i", str(origen),
                          "-i", str(wav), "-t", f"{dur}", "-filter_complex", grafo, "-map", "[m]",
                          "-ar", "44100", "-ac", "2", str(aud)], cwd=trabajo)
            else:
                # el audio de cada escena dura exactamente lo mismo que su video
                ejecutar(["ffmpeg", "-y", "-v", "error", "-i", str(wav), "-af", "apad",
                          "-t", f"{dur}", "-ar", "44100", "-ac", "2", str(aud)], cwd=trabajo)
            aviso = "  <-- LA VOZ NO CABE: acorta el texto o alarga el clip" if dur_voz > dur else ""
            print(f"  {nombre:9s} video {dur:5.1f} s | voz {dur_voz:5.1f} s{aviso}")
            bloques.append((inicio_escena + 0.2, inicio_escena + min(dur_voz + 0.2, dur), texto))

        lista_video.append(seg.name)
        lista_audio.append(aud.name)
        inicio_escena += dur

    total = inicio_escena
    print(f"\nDuración total: {total:.1f} s (la guía pide {DURACION_MIN}-{DURACION_MAX} s)")
    if not DURACION_MIN <= total <= DURACION_MAX:
        print("AVISO: la duración está fuera del rango. Ajusta los recortes de ESCENAS.")

    print("\n== 3) Subtítulos ==")
    escribir_srt(bloques, trabajo / "subs.srt")
    print("  subs.srt creado")

    print("\n== Unión de escenas ==")
    for nombre_lista, archivos, salida in (("lista_video.txt", lista_video, "video_unido.mp4"),
                                           ("lista_audio.txt", lista_audio, "audio_unido.wav")):
        (trabajo / nombre_lista).write_text("".join(f"file '{a}'\n" for a in archivos), encoding="utf-8")
        ejecutar(["ffmpeg", "-y", "-v", "error", "-f", "concat", "-safe", "0",
                  "-i", nombre_lista, "-c", "copy", salida], cwd=trabajo)

    print("\n== 4) Normalización de audio ==")
    antes = volumen_lufs(trabajo / "audio_unido.wav")
    normalizar(trabajo / "audio_unido.wav", trabajo / "audio_normalizado.wav", trabajo)
    despues = volumen_lufs(trabajo / "audio_normalizado.wav")
    print(f"  volumen antes: {antes:.1f} LUFS | después: {despues:.1f} LUFS (objetivo -16)")

    print("\n== 5) Compresión en varias resoluciones ==")
    for alto, crf in SALIDAS:
        destino = final / f"promocional_{alto}p.mp4"
        exportar("video_unido.mp4", "audio_normalizado.wav", "subs.srt", alto, crf, destino, trabajo)
        mb = destino.stat().st_size / 1024 / 1024
        print(f"  {destino.name}: {duracion(destino):.1f} s, {mb:.1f} MB")

    print(f"\nListo. Videos finales en: {final}")


if __name__ == "__main__":
    main()