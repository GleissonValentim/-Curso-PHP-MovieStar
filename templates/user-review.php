<?php

require_once("./models/User.php");

$userModel = new User();

$fullName = $userModel->getFullName($review->user);

// Checar se o usuário possui uma imagem
if ($review->user->image == "") {
    $review->user->image = "user.png";
}

?>

<div class="col md-12 review">
    <div class="row">
        <div class="col-md-1 image-review">
            <img class="profile-image-container review-image" src="<?= $BASE_URL ?>img/users/<?= $review->user->image ?>" alt="Imagem do Usuário">
        </div>
        <div class="col-md-9 author-details-container">
            <h4 class="author-name">
                <a href="<?= $BASE_URL ?>profile.php?id=<?= $review->user->id ?>"><?= $fullName ?></a>
            </h4>
            <p><i class="fas fa-star"></i> <?= $review->rating ?></p>
        </div>
        <div class="col-md-12">
            <p class="coment-title">Comentário:</p>
            <p><?= $review->review ?></p>
        </div>
    </div>
</div>
