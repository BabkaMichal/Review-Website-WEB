<?php
/////////////////////////////////////////////////////////////
/////////// Sablona pro zobrazeni uvodni stranky  ///////////
/////////////////////////////////////////////////////////////

//// vypis sablony
// urceni globalnich promennych, se kterymi sablona pracuje
global $tplData;

// pripojim objekt pro vypis hlavicky a paticky HTML
require(DIRECTORY_VIEWS . "/TemplateBasics.class.php");
$tplHeaders = new TemplateBasics();

?>
<!-- ------------------------------------------------------------------------------------------------------- -->

<!-- Vypis obsahu sablony -->
<?php

// hlavicka

$tplHeaders->getHTMLHeader($tplData['title']);

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error_message']) . "</div>";
    unset($_SESSION['error_message']);
}

// vypis her
$tplHeaders->HTMLGameStart();
$IMAGE_ROOT = 'app/images/';

//nastaveni purifier třídy -> import
$config = \HTMLPurifier_Config::createDefault();
$purifier = new \HTMLPurifier($config);

if (array_key_exists('games', $tplData)) {
    foreach ($tplData['games'] as $games) {
        //cleaning url
        $cleanPath = ltrim($games['image_url'], '/');
        $correctUrl = $IMAGE_ROOT . $cleanPath;

        //cleaning description for safety
        $clean_html = $purifier->purify($games['description']);

        $tplHeaders->getHTMLGame($games['id_game'],
                $games['title'],
                $games['developer'],
                $clean_html,
                $correctUrl,
                $games['average_rating'],
                $games['total_ratings'],
                $games['id_user']);
    }
} else {
    echo "Hry nenalezeny!";
}
$tplHeaders->HTMLGameEnd();

//pro zobrazeni info o hrach
$tplHeaders->HTMLReviewModal();

// paticka
$tplHeaders->getHTMLFooter();

$tplHeaders->HTMLReviewModalScript();
?>
