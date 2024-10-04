<?php
require_once(__DIR__ . '/isConnect.php');
?>

<form action="comments_post_create.php" method="POST">
    <div class="mb-3 visually-hidden">
        <input class="form-control" type="text" name="manga_id" value="<?php echo($manga['manga_id']); ?>" />
    </div>
    <div class="mb-3">
        <label for="review" class="form-label">Evaluez le manga (de 1 à 5)</label>
        <input type="number" class="form-control" id="review" name="review" min="1" max="5" step="1" required />
    </div>
    <div class="mb-3">
        <label for="comment" class="form-label">Postez un commentaire</label>
        <!-- Ajout des attributs minlength et maxlength pour la validation côté client -->
        <textarea class="form-control" placeholder="Soyez respectueux/se, nous sommes humain(e)s." id="comment" name="comment" minlength="1" maxlength="300" required></textarea>
        <div class="form-text">Le commentaire doit contenir entre 1 et 300 caractères.</div>
    </div>
    <button type="submit" class="btn btn-primary">Envoyer</button>
</form>
