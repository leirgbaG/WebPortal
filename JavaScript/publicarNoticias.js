// Reemplazar saltos de Línea 
const textInput = document.querySelector('#descripcion');
const outputContainer = document.querySelector('#enviable');

textInput.addEventListener('input', function(){
    const input = textInput.value;
    const formattedText = input.replace(/\n/g, '<br>');
    outputContainer.innerHTML = formattedText;
});

document.querySelector('#multimedia').addEventListener('change', function(event) {
    const files = event.target.files;
    const imagePreview = document.querySelector('#imgsContainer');
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
