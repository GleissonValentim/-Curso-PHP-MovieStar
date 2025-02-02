<?php
require_once("./templates/header.php");
require_once("./models/Movie.php");
require_once("./dao/MovieDAO.php");
require_once("./dao/ReviewDAO.php");
require_once("./models/Message.php"); 

$message = new Message($BASE_URL);
$movieDAO = new MovieDAO($conn, $BASE_URL);
$reviewDAO = new ReviewDAO($conn, $BASE_URL);

$movie = null;

// Pega o ID do filme
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {

    $message->setMessage("O filme não foi encontrado!", "error", "index.php");

}

$movie = $movieDAO->findById($id);

// Verifica se o filme existe 
if (!$movie) {

    $message->setMessage("O filme não foi encontrado!", "error", "index.php");

}

// Verifica o formanto do video
if (strpos($movie->trailer, "youtu.be/") !== false) {
    $videoId = substr(parse_url($movie->trailer, PHP_URL_PATH), 1);
    $movie->trailer = "https://www.youtube.com/embed/" . $videoId;
}

// Checar se o filme tem imagem
if($movie->image == ""){
    $movie->image = "movie_cover.jpg";
}

// Checar se o filme é do usuario
$userOwnsMovie = false;

if(!empty($userData)) {

    if($userData->id === $movie->users_id) {
        $userOwnsMovie = true;
    }

    // Resgatar as revies do filme 
    $alreadyReviwed = $reviewDAO->hasAlreadyReviewed($id, $userData->id);
}

// Resgatar as reviews do filme
$movieReviews = $reviewDAO->getMoviesReview($id);

?>

<div id="main-container" class="container-fluid">
    <div class="row">
        <div class="offset-md-1 col-md-6 movie-container">
            <h1 class="page-title"><?= $movie->title ?></h1>
            <p class="movie-details">
                <span>Duração: <?= $movie->length ?></span>
                <span class="pipe"></span>
                <span><?= $movie->category ?></span>
                <span class="pipe"></span>
                <span><i class="fas fa-star"></i> <?= $movie->rating ?></span>
            </p>
            <iframe src="<?= $movie->trailer ?>" width="560" height="315" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope;
            picture-in-picture" allowfullscreen></iframe>
            <p><?= $movie->description ?></p>
        </div>
        <div class="col-md-4">
            <div class="movie-image-container">
                <img src="<?= $BASE_URL ?>/img/movies/<?= $movie->image ?>" alt="Imagem do filme">
            </div>
        </div>
        <div class="offset-md-1 col-md-10 mt-5" id="reviews-container">
            <h3 class="reviews-title">Avaliações:</h3>
            <!-- Verifica se habilita a review para o usuario ou não -->
             <?php if(!empty($userData) && !$userOwnsMovie && !$alreadyReviwed): ?>
             <div class="col-md-12" id="review-form-container">
                <h4>Envie sua avaliação:</h4>
                <p class="page-description">Preencha o formuário com a nota e comentário sobre o filme</p>
                <form action="<?= $BASE_URL ?>review_process.php" id="review-form" method="POST">
                    <input type="hidden" name="type" value="create">
                    <input type="hidden" name="movies_id" value="<?= $movie->id ?>">
                    <div class="form-group">
                        <label for="rating">Nota do filme:</label>
                        <select name="rating" id="rating" class="form-control">
                            <option value="Selecione"></option>
                            <option value="10">10</option>
                            <option value="9">9</option>
                            <option value="8">8</option>
                            <option value="7">7</option>
                            <option value="6">6</option>
                            <option value="5">5</option>
                            <option value="4">4</option>
                            <option value="3">3</option>
                            <option value="2">2</option>
                            <option value="1">1</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="review">Seu comentário:</label>
                        <textarea name="review" id="review" rows="5" class="form-control"
                        placeholder="Oque voçe achou do filme?"></textarea>
                    </div>
                    <input type="submit" class="btn card-btn" value="Enviar comentário">
                </form>
             </div>
             <?php endif; ?>
             <!-- comentários -->
             <?php foreach($movieReviews as $review): ?>
                <?php require("templates/user-review.php"); ?>
             <?php endforeach; ?>
             <?php if(count($movieReviews) == 0): ?>
                <br>
                <p class="empty-list">Não há comentários para este filme ainda...</p>
             <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once("./templates/footer.php");
?>
