<?php
global $tplData;
require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

$tplHeaders->getHTMLHeader('Upravit hru'); // Změněn titulek stránky
?>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4">Upravit hru</h2>

                    <?php if (isset($tplData['error_message'])): ?>
                        <div class="alert alert-danger text-center">
                            <?= htmlspecialchars($tplData['error_message']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($tplData['success_message'])): ?>
                        <div class="alert alert-success text-center">
                            <?= htmlspecialchars($tplData['success_message']) ?>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?page=update" method="POST" enctype="multipart/form-data">

                        <input type="hidden" name="action" value="update_game">

                        <input type="hidden" name="id_game" value="<?= $tplData['game']['id_game'] ?>">

                        <div class="mb-3">
                            <label class="form-label" for="title">Název hry</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   value="<?= htmlspecialchars($tplData['game']['title'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="developer">Vývojář</label>
                            <input type="text" class="form-control" id="developer" name="developer"
                                   value="<?= htmlspecialchars($tplData['game']['developer'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Popis</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($tplData['game']['description'] ?? '') ?></textarea>
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
                            <p class="text-muted small mb-2">Pokud nechcete obrázek měnit, nechte toto pole prázdné.</p>

                            <input type="file" class="form-control" id="image" name="image" accept="image/*">

                            <div class="row mt-3">
                                <div class="col-6 text-center">
                                    <span class="d-block mb-2 fw-bold">Současný obrázek:</span>
                                    <?php if (!empty($tplData['game']['image_url'])): ?>
                                        <img src="app/images/<?= htmlspecialchars($tplData['game']['image_url']) ?>"
                                             alt="Současný obrázek" class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                    <?php else: ?>
                                        <p>Žádný obrázek.</p>
                                    <?php endif; ?>
                                </div>

                                <div class="col-6 text-center">
                                    <span class="d-block mb-2 fw-bold">Nový náhled:</span>
                                    <img id="imagePreview" src="#" alt="Náhled nového"
                                         class="img-fluid rounded shadow-sm d-none" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>

                        <script>
                            document.getElementById('image').addEventListener('change', function (event) {
                                const file = event.target.files[0];
                                const preview = document.getElementById('imagePreview');

                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = function (e) {
                                        preview.src = e.target.result;
                                        preview.classList.remove('d-none');
                                    }
                                    reader.readAsDataURL(file);
                                } else {
                                    preview.src = "#";
                                    preview.classList.add('d-none');
                                }
                            });
                        </script>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Uložit změny</button>
                            <a href="index.php" class="btn btn-secondary">Zrušit</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php $tplHeaders->getHTMLFooter(); ?>