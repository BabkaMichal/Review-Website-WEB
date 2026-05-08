<?php

/**
 * Trida vypisujici HTML hlavicku a paticku stranky.
 */
class TemplateBasics
{

    /**
     *  Vrati vrsek stranky az po oblast, ve ktere se vypisuje obsah stranky.
     * @param string $pageTitle Nazev stranky.
     */
    public function getHTMLHeader(string $pageTitle)
    {
        $role = $_SESSION['user']['role'] ?? 'guest';
        ?>
        <!doctype html>
        <html lang="cs" class="h-100">
        <head>
            <meta charset='utf-8'>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <!-- Bootstrap -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
                  integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
                  crossorigin="anonymous">

            <!-- FontAwesome -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
                  integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
                  crossorigin="anonymous" referrerpolicy="no-referrer"/>


            <title><?php echo htmlspecialchars($pageTitle); ?></title>
        </head>

        <body class="d-flex flex-column h-100">

        <!-- Head -->
        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
                <div class="container">
                    <!-- jmeno webu -->
                    <a class="navbar-brand" href="index.php">
                        <i class="fas fa-gamepad"></i> <!-- ikonka -->
                        GamePortál
                    </a>

                    <!-- Tlačítko pro mobilní menu -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                            aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Hlavní navigace -->
                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <?php
                            if (defined('WEB_PAGES')) {
                                foreach (WEB_PAGES as $key => $pInfo) {
                                    //pro vsechny
                                    if ($key == 'uvod') {
                                        echo "<li class='nav-item'><a class='nav-link' href='index.php?page=$key'>" . htmlspecialchars($pInfo['title']) . "</a></li>";
                                        //pro prihlasene
                                    }//pouze superadmin
                                    elseif($role === 'superadmin' && $key == 'sprava') {
                                        echo "<li class='nav-item'>"
                                                . "<a class='nav-link' href='index.php?page=$key'>"

                                                . "<i class='fas fa-user-shield'></i> "
                                                . htmlspecialchars($pInfo['title'])
                                                . "</a></li>";
                                    } else {
                                        continue;
                                    }
                                }
                            }
                            ?>
                        </ul>

                        <!-- Tlačítka pravá strana  -->
                        <div class="navbar-nav ms-auto">
                            <?php if (isset($_SESSION['user'])) : ?>
                                <a class="btn btn-outline-light me-2" href="index.php?page=add_review">Přidat
                                    příspěvek</a>
                                <a class="btn btn-danger" href="index.php?page=login&action=logout">Odhlásit se
                                    (<?php echo htmlspecialchars($_SESSION['user']['login']); ?>)</a>
                            <?php else: ?>
                                <a class="btn btn-outline-light me-2" href="index.php?page=login">Přihlásit se</a>
                                <a class="btn btn-primary" href="index.php?page=register">Registrovat</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
        </header>


        <main class="container my-4">

        <!-- Titulek stránky -->
        <h1 class="mb-4"><?php echo htmlspecialchars($pageTitle); ?></h1>

        <?php
    }


    /**
     *  Vykreslení jednotlivých her a přidání do kontejneru
     */
    function HTMLGameStart()
    {
        echo "<div class='row g-4'>";
    }

    function HTMLGameEnd()
    {
        echo "</div>";
    }

    function getHTMLGame($id_game, $title, $developer, $description, $imageUrl, $averageRating, $totalRating, $authorId)
    {

        // --- zabezpečení ---
        $id_game_safe = (int)$id_game;
        $title_safe = htmlspecialchars($title);
        $developer_safe = htmlspecialchars($developer);

        //predtim cistime purifierem, nema spatny kod -> je safe
        $description_safe = $description;

        $description_safe_data = htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); // Pro data atribut
        $imageUrl_safe = htmlspecialchars($imageUrl);
        $averageRating_safe = htmlspecialchars($averageRating);
        $totalRatings_safe = (int)$totalRating;

        //pomocne promnene pro role
        $role = $_SESSION['user']['role'] ?? 'guest';
        $currentUserId = $_SESSION['user']['id'] ?? null;
        $authorIdSafe = (int)$authorId;
        $isAdmin = in_array($role, ['admin', 'superadmin']);
        $isAuthor = ($currentUserId !== null && $currentUserId == $authorIdSafe);
        ?>


        <div class="col-md-6">
            <div class="card shadow-sm game-card-custom h-100 position-relative">

                <?php
                if ($isAdmin || $isAuthor) {
                    //obalovaci div
                    echo "<div class='position-absolute top-0 start-0 m-2 d-flex flex-column gap-2' style='z-index: 10;'>";

                    //smazat -> pouze admin
                    if ($isAdmin) {
                        echo "<a href='index.php?page=delete_game&id=$id_game_safe' "
                                . "class='btn btn-danger btn-sm' "
                                . "onclick='return confirm(\"Opravdu chcete smazat tento příspěvek?\");'>"
                                . "<i class='fas fa-trash'></i> Odebrat</a>";
                    }

                    //upravit -> admin/autor
                    echo "<a href='index.php?page=update&id=$id_game_safe' "
                            . "class='btn btn-warning btn-sm'>"
                            . "<i class='fas fa-edit'></i> Upravit</a>";

                    echo "</div>";
                }
                ?>

                <!-- ajax vec -->
                <button
                        type="button"
                        class="btn btn-primary btn-sm position-absolute top-0 end-0 m-2"
                        style="z-index: 10;"
                        data-bs-toggle="modal"
                        data-bs-target="#reviewModal"
                        data-game-id="<?= $id_game_safe ?>"
                        data-game-title="<?= $title_safe ?>"
                        data-game-image="<?= $imageUrl_safe ?>"
                <!--data-game-description="<?= $description_safe_data ?>"-->
                >
                <i class="fas fa-plus"></i> Přidat hodnocení
                </button>

                <div class="game-image-container">
                    <img src="<?= $imageUrl_safe ?>" class="card-img-top" alt="Cover art for <?= $title_safe ?>">
                </div>

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0"><?= $title_safe ?></h5>

                        <span class="game-rating">
                        <?php
                        if ($totalRatings_safe > 0) {
                            $formattedRating = number_format((float)$averageRating_safe, 1);
                            echo '<i class="fas fa-star"></i> ' . $formattedRating . " ($totalRatings_safe)";
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </span>
                    </div>

                    <div class="game-details">
                        <p class="game-developer text-muted">
                            <strong>Developer:</strong> <?= $developer_safe ?>
                        </p>
                        <p class="game-description">
                            <strong>Description:</strong><br>
                            <?= nl2br($description_safe) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    //review okenko
    function HTMLReviewModal()
    {

        echo '
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="reviewForm" action="index.php?page=post_review" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">Přidat recenzi pro: [Název Hry]</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img id="modalGameImage" src="" class="img-fluid rounded" alt="Game cover">
                            </div>
                            <div class="col-md-8">
                                <p id="modalGameDescription"></p>
                            </div>
                        </div>

                        <hr>
                        
                        <h5>Vaše hodnocení</h5>
                        <div id="modalRatingStars">
                            <select class="form-select" name="rating" required>
                                <option value="" disabled selected>Vyberte hodnocení...</option>
                                <option value="1">★☆☆☆☆</option>
                                <option value="2">★★☆☆☆</option>
                                <option value="3">★★★☆☆</option>
                                <option value="4">★★★★☆</option>
                                <option value="5">★★★★★</option>
                            </select>
                        </div>

                        <input type="hidden" id="modalGameId" name="id_game" value="">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zavřít</button>
                        <button type="submit" class="btn btn-primary">Odeslat recenzi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
    }

    //Java script pro zobrazení review okenka
    function HTMLReviewModalScript()
    {
//javascript + ajax + api call
        echo "
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        
        var reviewModal = document.getElementById('reviewModal');
        
        if (reviewModal) {
            reviewModal.addEventListener('show.bs.modal', function (event) {
                
                var button = event.relatedTarget;
                
                var gameId = button.getAttribute('data-game-id');
                var gameTitle = button.getAttribute('data-game-title');
                var gameImage = button.getAttribute('data-game-image');
                //var gameDescription = button.getAttribute('data-game-description');

                var modalTitle = reviewModal.querySelector('.modal-title');
                var modalImage = reviewModal.querySelector('#modalGameImage');
                var modalDescription = reviewModal.querySelector('#modalGameDescription');
                var modalGameIdInput = reviewModal.querySelector('#modalGameId');

                modalTitle.textContent = 'Přidat recenzi pro: ' + gameTitle;
                modalImage.src = gameImage;
                //modalDescription.textContent = gameDescription;
                modalGameIdInput.value = gameId;
                
                //nastavíme placeholder popis
                modalDescription.textContent = 'Načítám detailní popis...';
                
                //přes ajax a api získáme popis, kdyby byl dlouhý a načítal se déle
                fetch('api.php?id=' + gameId)
                        .then(response => response.json())
                        .then(data => {
                            modalDescription.textContent = data.description;
                        })
                        .catch(err => {
                            modalDescription.textContent = 'Popis se nepodařilo načíst.';
                        });
                });
        }
    });
    </script>
    ";
    }

    /**
     *  Vrati paticku stranky.
     */
    public function getHTMLFooter()
    {
        ?>
        </main>
        <footer class="footer mt-auto py-3 bg-light border-top">
            <div class="container text-center">
                <span class="text-muted">Autor: Michal Babka
                    | Kontakty: <i class="fa-brands fa-instagram"></i> michal.babka.104
                    | Vytvořeno s pomocí Bootstrap 5</span>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
                crossorigin="anonymous"></script>
        </body>
        </html>
        <?php
    }
}

?>