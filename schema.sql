DROP TABLE IF EXISTS Notes;
DROP TABLE IF EXISTS Contacts;
DROP TABLE IF EXISTS Users;

CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    password VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    role VARCHAR(255),
    created_at DATETIME
);

INSERT INTO Users (firstname, lastname, password, email, role, created_at) 
VALUES ('Admin', 'User', '$2a$12$62yM./CHHJn56SEHRWb4TuZfpSWAklSJiQ7H1BakorNYESiqllDnm', 'admin@project2.com', 'admin', NOW());

CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    telephone VARCHAR(20),
    company VARCHAR(255),
    type VARCHAR(50),
    assigned_to INT,
    created_by INT,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE Notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT,
    comment TEXT,
    created_by INT,
    created_at DATETIME,
    FOREIGN KEY (contact_id) REFERENCES Contacts(id) ON DELETE CASCADE
);
