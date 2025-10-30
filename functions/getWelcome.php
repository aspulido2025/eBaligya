<?php
    // Production-grade randomness using the Mersenne Twister PRNG.
    // random_int($min, $max) → cryptographically secure integer between $min and $max
    // Secure randomness (cannot be predicted).
    // Every call will show either English or Tagalog randomly, and then a random phrase from that language.
    // greets users in a fresh way every time

    function getWelcome(): string {
        $phrases = [
            'en' => [
                "Welcome! Strive for progress, not perfection.",
                "Welcome back! Ready to learn something new today?",
                "Education is the passport to the future.",
                "Your journey of knowledge starts here.",
                "Small steps every day lead to big achievements.",
                "Empowering students, one login at a time.",
                "Great to see you! Let's make today productive.",
                "The roots of education are bitter, but the fruit is sweet.",
                "Knowledge is power — use it wisely.",
                "Welcome! Strive for progress, not perfection.",
                "Learning never exhausts the mind.",
                "Every day is a chance to learn and grow.",
                "Unlock your potential — your records are safe here."
            ],
            'tl' => [
                "Maligayang pagbabalik! Handa ka na bang matuto ngayon?",
                "Ang edukasyon ay pasaporte tungo sa kinabukasan.",
                "Dito nagsisimula ang iyong paglalakbay sa kaalaman.",
                "Maliit na hakbang araw-araw, malaking tagumpay kalaunan.",
                "Pinalalakas ang mga estudyante, isang login sa bawat oras.",
                "Buti naman at nandito ka! Gawin nating produktibo ang araw.",
                "Mapait ang ugat ng edukasyon, ngunit matamis ang bunga.",
                "Ang kaalaman ay kapangyarihan — gamitin ito nang tama.",
                "Maligayang pagdating! Sikapin ang progreso, hindi perpeksiyon.",
                "Ang pag-aaral ay hindi nakakapagod sa isipan.",
                "Bawat araw ay pagkakataon upang matuto at lumago.",
                "Buksan ang iyong potensyal — ligtas ang iyong talaan dito."
            ]
        ];

        // Pick a random language
        $languages = array_keys($phrases);
        $lang = $languages[random_int(0, count($languages) - 1)];

        // Pick a random phrase from that language
        $index = random_int(0, count($phrases[$lang]) - 1);

        return $phrases[$lang][$index];
    }
