<script>
(function () {
    document.querySelectorAll('[data-drop]').forEach(function (box) {
        var input = box.querySelector('[data-input]');
        var cam = box.querySelector('[data-camera]');
        var thumb = box.querySelector('[data-thumb]');
        var status = box.querySelector('[data-status]');
        var err = box.querySelector('[data-error]');
        var maxBytes = parseFloat(box.dataset.maxMb) * 1048576;
        var min = parseInt(box.dataset.min, 10);
        var allowPdf = box.dataset.pdf === '1';
        var okTypes = ['image/jpeg', 'image/png', 'image/webp'].concat(allowPdf ? ['application/pdf'] : []);

        function fail(msg) {
            input.value = '';
            box.classList.add('is-bad');
            box.classList.remove('is-has');
            err.textContent = msg;
        }

        function check(file) {
            err.textContent = '';
            box.classList.remove('is-bad');
            if (!file) { return; }
            if (okTypes.indexOf(file.type) === -1) { return fail('That file type is not accepted. Use JPG, PNG, WebP' + (allowPdf ? ' or PDF.' : '.')); }
            if (file.size > maxBytes) { return fail('That file is ' + (file.size / 1048576).toFixed(1) + ' MB. The limit is ' + box.dataset.maxMb + ' MB.'); }
            if (file.type === 'application/pdf') {
                thumb.innerHTML = '<span>PDF</span>';
                status.textContent = file.name + ' is ready to upload.';
                box.classList.add('is-has');
                return;
            }
            var url = URL.createObjectURL(file);
            var img = new Image();
            img.onload = function () {
                if (Math.max(img.naturalWidth, img.naturalHeight) < min) {
                    URL.revokeObjectURL(url);
                    return fail('That image is only ' + img.naturalWidth + ' x ' + img.naturalHeight + ' px. The long side must be at least ' + min + ' px so the text is readable.');
                }
                thumb.innerHTML = '';
                img.alt = 'Preview of ' + file.name;
                thumb.appendChild(img);
                status.textContent = file.name + ' (' + img.naturalWidth + ' x ' + img.naturalHeight + ' px) is ready to upload.';
                box.classList.add('is-has');
            };
            img.onerror = function () { fail('This image could not be read. Try a different file.'); };
            img.src = url;
        }

        input.addEventListener('change', function () { check(input.files[0]); });
        if (cam) {
            cam.addEventListener('change', function () {
                if (!cam.files[0]) { return; }
                try {
                    var dt = new DataTransfer();
                    dt.items.add(cam.files[0]);
                    input.files = dt.files;
                    check(input.files[0]);
                } catch (e) { fail('Your browser could not attach the photo. Use the file picker instead.'); }
            });
        }
    });
})();
</script>
