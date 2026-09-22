DROP DATABASE IF EXISTS quizmaker;
CREATE DATABASE quizmaker;
USE quizmaker;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255),
    password_hash VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(255),
    bio TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    aangemaakt_op TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quizzes (
    quiz_id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    beschrijving TEXT NOT NULL,
    image_url VARCHAR(255),
    aangemaakt_op TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE vragen (
    vraag_id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    vraagtekst TEXT NOT NULL,
    vraag_type ENUM('single','multiple') DEFAULT 'single',
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
);

CREATE TABLE antwoorden (
    antwoord_id INT AUTO_INCREMENT PRIMARY KEY,
    vraag_id INT NOT NULL,
    antwoordtekst VARCHAR(255) NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (vraag_id) REFERENCES vragen(vraag_id) ON DELETE CASCADE
);

CREATE TABLE user_quiz_scores (
    score_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    tijd INT DEFAULT 0,
    gespeeld_op TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
);

CREATE TABLE badges (
    badge_id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(255) NOT NULL,
    beschrijving TEXT,
    icoon VARCHAR(255)
);

CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    badge_id INT NOT NULL,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(badge_id) ON DELETE CASCADE
);

CREATE TABLE leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(quiz_id) ON DELETE CASCADE
);

INSERT INTO badges (naam, beschrijving, icoon) VALUES
('Eerste Quiz', 'Je hebt je eerste quiz gespeeld.', 'https://images.stockcake.com/public/b/2/c/b2c198f6-6374-41a4-ac90-48f4256e920d_large/spider-man-action-pose-stockcake.jpg'),
('Perfecte Score', 'Je hebt 100% gehaald op een quiz.', 'https://static.dc.com/2024-12/2024_12_19_Superman_BlogRoll_Mobile_4x3.jpg?w=640'),
('10 Quizzes', 'Je hebt 10 quizzen gespeeld.', 'https://4kwallpapers.com/images/wallpapers/ben-10-5k-cartoon-2880x1800-18954.jpg'),
('Snelheidsduivel', 'Je hebt een extreem snelle tijd gehaald.', 'https://i1.sndcdn.com/artworks-cc2rymyxRb1I9EtA-XQyfpw-t1080x1080.jpg');

