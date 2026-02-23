CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name   VARCHAR(255) NOT NULL,
  pub_date DATE NULL,
  genre  VARCHAR(100) NOT NULL,
  author VARCHAR(255) NOT NULL,
  image_url VARCHAR(800) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO books (name, pub_date, genre, author, image_url) VALUES
('Dune', '1965-08-01', 'Sci-Fi', 'Frank Herbert', 'https://covers.openlibrary.org/b/isbn/9780441172719-L.jpg');