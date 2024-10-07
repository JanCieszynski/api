<?php
if(!empty($_POST)) {
    //coś przyszło postem
    $postTitle = $_POST['postTitle'];
    $postDescription = $_POST['postDescription'];
    //wgrywanie pliku
    //zdefiniuj folder docelowy
    $targetDirectory = "img/";
    //użyj oryginalnej nazwy pliku
    //$fileName = $_FILES['file']['name'];
    //modyfikacja - użyj sha256
    $fileName = hash('sha256', $_FILES['file']['name'].microtime());
    
    //przesuń plik z lokalizacji tymczasowej do docelowej
    //move_uploaded_file($_FILES['file']['tmp_name'], $targetDirectory.$fileName);
    //zmiana - użyj imagewebp do zapisania

    //po 0!: wczytaj zawartość pliku graficznego do stringa
    $fileString = file_get_contents($_FILES['file']['tmp_name']);

    //po 1!: wczytaj otrzymany z formularza obrazek używając biblioteki GD do obiektu klasy GDImage
    $gdImage = imagecreatefromstring($fileString);

    //przygotuj pełny url pliku
    $finalUrl = "http://localhost/cms/img/".$fileName.".webp";
    //imagewebp nie umie z http - link wewnętrzny
    $internalUrl = "img/".$fileName.".webp";

    //po 2!: zapisz obraz skonwertowany do webp pod nową nazwą pliku + rozszerzenie webp
    imagewebp($gdImage, $internalUrl);

    //dopisz posta do bazy
    //tymczasowo - authorID
    $authorID = 1;


    $db = new mysqli('localhost', 'root', '', 'forum-wedkarskie');
    $q = $db->prepare("INSERT INTO post (author, imgUrl, title) VALUES (?, ?, ?)");
    //pierwszy atrybut jest liczba, dwa pozostale tekstem wiec integer string string
    $q->bind_param("iss", $authorID, $finalUrl, $postTitle);
    $q->execute();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj nowy post</title>
</head>
<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <label for="postTitleInput">Tytuł posta:</label>
        <input type="text" name="postTitle" id="postTitleInput">
        <br>
        <label for="postDescriptionInput">Opis posta:</label>
        <input type="text" name="postDescription" id="postDescriptionInput">
        <br>
        <label for="fileInput">Obrazek:</label>
        <input type="file" name="file" id="fileInput">
        <br>
        <input type="submit" value="Wyślij!">
    </form>
</body>
</html>