document.getElementById('multimedia').addEventListener('change', function(event) {
    const files = event.target.files;
    const imagePreview = document.getElementById('imgsContainer');
    imagePreview.innerHTML = '';

    Array.from(files).forEach(file => {
        const reader = new FileReader();

        reader.onload = function(e) {
            const fileType = file.type;
            let mediaElement;

            if (fileType.startsWith('image/')) {
                mediaElement = document.createElement('img');
                mediaElement.src = e.target.result;
                mediaElement.alt = 'Previsualización de Imagen';
            } else if (fileType.startsWith('video/')) {
                mediaElement = document.createElement('video');
                mediaElement.src = e.target.result;
                mediaElement.controls = true;
            }

            imagePreview.appendChild(mediaElement);
        }

        reader.readAsDataURL(file);
    });
});
