  DROP TABLE IF EXISTS reserved;
  DROP TABLE IF EXISTS books;
  DROP TABLE IF EXISTS category;
  DROP TABLE IF EXISTS users;
  
  
  CREATE TABLE users(
    userName varchar(15) PRIMARY KEY ,
    passwords varchar(30) NOT NULL,
    firstName varchar(10) NOT NULL,
    surName varchar(20) NOT NULL,
    addresses text NOT NULL,
    town text ,
    city text NOT NULL,
    telephone integer NOT NULL);
    
  CREATE TABLE category(
    categoryID integer PRIMARY KEY ,
    categoryDesc varchar(15) NOT NULL);

  CREATE TABLE books(
    ISBN varchar(15) PRIMARY KEY ,
    bookTitle text NOT NULL,
    author varchar(30) NOT NULL,
    editor integer NOT NULL,
    yearMake integer NOT NULL,
    category integer NOT NULL,
    reserved char NOT NULL,
    FOREIGN KEY (category) REFERENCES category(categoryID));
    
  CREATE TABLE reserved(
    ISBN varchar(15) UNIQUE NOT NULL,
    userName varchar(15) UNIQUE NOT NULL,
    reservedDate date NOT NULL,
    FOREIGN KEY (ISBN) REFERENCES books(ISBN),
    FOREIGN KEY (userName) REFERENCES users(userName),
    PRIMARY KEY(ISBN,userName));
    