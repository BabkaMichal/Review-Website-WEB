<?php
global $tplData;
require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

$tplHeaders->getHTMLHeader($tplData['title']);
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Přidat novou hru</h2>

                <?php if (isset($tplData['error_message'])): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($tplData['error_message']) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=add_review" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add_game">

                    <div class="mb-3">
                        <label class="form-label" for="title">Název hry</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="developer">Vývojář</label>
                        <input type="text" class="form-control" id="developer" name="developer" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">Popis</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>

                    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

                    <script>
                        ClassicEditor
                            .create(document.querySelector('#description'))
                            .catch(error => {
                                console.error(error);
                            });
                    </script>

                    <div class="mb-4">
                        <label class="form-label" for="image">Obrázek hry</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>

                        <!-- Obrázek pro náhled -->
                        <div class="mt-3 text-center">
                            <img id="imagePreview" src="#" alt="Náhled obrázku"
                                 class="img-fluid rounded shadow-sm d-none" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Obrázek pro náhled -->
                    <script>
                        document.getElementById('image').addEventListener('change', function (event) {
                            const file = event.target.files[0];
                            const preview = document.getElementById('imagePreview');

                            if (file) {
                                const reader = new FileReader();

                                reader.onload = function (e) {
                                    preview.src = e.target.result;
                                    preview.classList.remove('d-none'); // zobrazíme obrázek
                                }

                                reader.readAsDataURL(file);
                            } else {
                                preview.src = "#";
                                preview.classList.add('d-none'); // skryjeme, pokud nic nevybráno
                            }
                        });
                    </script>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Přidat hru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $tplHeaders->getHTMLFooter(); ?>
