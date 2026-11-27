<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recorrido del taller · Artesanías Mayas</title>
    <script src="https://cdn.jsdelivr.net/npm/aframe@1.7.0/dist/aframe.min.js"></script>
   <script>
    AFRAME.registerComponent('punto-info', {
        schema: { titulo: { type: 'string' }, texto: { type: 'string' } },
        init: function () {
            this.el.addEventListener('click', () => {
                const t = this.data.titulo + '. ' + this.data.texto;
                document.querySelector('#panel-html').textContent = t;
                document.querySelector('#panel').setAttribute('value', t.normalize('NFD').replace(/[\u0300-\u036f]/g, ''));
                if ('speechSynthesis' in window) {
                    speechSynthesis.cancel();
                    const u = new SpeechSynthesisUtterance(t);
                    u.lang = 'es-MX';
                    speechSynthesis.speak(u);
                }
            });
        }
    });
</script>
</head>
<body>
<a-scene cursor="rayOrigin: mouse" raycaster="objects: .clickable">
    <a-assets>
        <div id="panel-html"
     style="position:fixed;left:50%;bottom:24px;transform:translateX(-50%);max-width:90%;
            background:rgba(0,0,0,.65);color:#fff;font:18px/1.4 system-ui,sans-serif;
            padding:12px 20px;border-radius:8px;text-align:center;z-index:10;pointer-events:none;">
    Camina con W A S D y toca las esferas doradas para escuchar cada pieza.
</div>
        <a-asset-item id="m-vasija" src="{{ asset('models/vasija.glb') }}"></a-asset-item>
        <a-asset-item id="m-mascara" src="{{ asset('models/mascara.glb') }}"></a-asset-item>
        <a-asset-item id="m-hamaca" src="{{ asset('models/hamaca.glb') }}"></a-asset-item>
    </a-assets>

    <a-light type="ambient" color="#ffffff" intensity="0.8"></a-light>
    <a-light type="directional" color="#ffffff" intensity="0.6" position="2 4 3"></a-light>

    <a-sky color="#f5ead4"></a-sky>
    <a-plane rotation="-90 0 0" width="10" height="10" color="#b08a5a"></a-plane>
    <a-box position="0 1.5 -5" width="10" height="3" depth="0.1" color="#e8d3a8"></a-box>
    <a-box position="-5 1.5 0" width="0.1" height="3" depth="10" color="#dcc391"></a-box>
    <a-box position="5 1.5 0" width="0.1" height="3" depth="10" color="#dcc391"></a-box>

    <a-cylinder position="-2 0.5 -3" radius="0.4" height="1" color="#6b4a2b"></a-cylinder>
    <a-entity gltf-model="#m-vasija" position="-2 1.15 -3"></a-entity>
    <a-sphere class="clickable" position="-2 1.9 -3" radius="0.1" color="#f2b134"
              punto-info="titulo: Vasija de barro; texto: Vasija de barro con grabados tradicionales."></a-sphere>

    <a-cylinder position="0 0.5 -3" radius="0.4" height="1" color="#6b4a2b"></a-cylinder>
    <a-entity gltf-model="#m-mascara" position="0 1.15 -3"></a-entity>
    <a-sphere class="clickable" position="0 1.9 -3" radius="0.1" color="#f2b134"
              punto-info="titulo: Máscara ceremonial; texto: Máscara de inspiración maya tallada y pintada a mano."></a-sphere>

    <a-cylinder position="1.15 0.65 -3" radius="0.05" height="1.3" color="#6b4a2b"></a-cylinder>
    <a-cylinder position="3.25 0.65 -3" radius="0.05" height="1.3" color="#6b4a2b"></a-cylinder>
    <a-entity gltf-model="#m-hamaca" position="2.2 0.9 -3"></a-entity>
    <a-sphere class="clickable" position="2.2 1.9 -3" radius="0.1" color="#f2b134"
              punto-info="titulo: Hamaca tejida; texto: Hamaca tejida a mano con hilo de colores."></a-sphere>

    <a-entity camera look-controls wasd-controls position="0 1.6 1.5">
    <a-entity id="panel-vr" visible="false">
        <a-plane position="0 -0.5 -1" width="1.7" height="0.35" color="#000000" opacity="0.6"></a-plane>
        <a-text id="panel" value="Toca las esferas doradas para escuchar cada pieza."
                position="0 -0.5 -0.99" width="1.6" align="center" wrap-count="48" color="#ffffff"></a-text>
    </a-entity>
</a-entity>
</a-scene>
</body>
</html>