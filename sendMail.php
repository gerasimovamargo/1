<?php

$subject = 'МОЄ ТЕСТОВЕ ПОВІДОМЛЕННЯ';

echo '============' . "\n";
echo $subject . "\n";
echo '============' . "\n";

$firstName = 'Рита';
$text1 = "Ім'я: {$firstName}" . "\n";
$text2 = "Електронна пошта: m.v.herasymova@student.khai.edu" . "\n";
$text3 = "Тема: {$subject}" . "\n";
$text4 = "Повідомлення: Ваш тестовий лист успішно відправлено!" . "\n";

$message = $text1 . $text2 . $text3 . $text4;
$message .= "Це тестове повідомлення!" . "\n";
$message .= "З найкращими побажаннями!";


$headers = "From: m.v.herasymova@student.khai.edu\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$to = 'myfriend@hotmail.com';
if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
    if (mail($to, $subject, $message, $headers)) {
        echo "Лист успішно відправлено!\n";
    } else {
        echo "Помилка: не вдалося відправити лист.\n";
    }
} else {
    echo "Помилка: неправильний формат email-адреси.\n";
}

?>
