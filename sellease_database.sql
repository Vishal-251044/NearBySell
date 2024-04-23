CREATE DATABASE SELLEASE;

USE SELLEASE;

CREATE TABLE Seller (
    SelleremailID VARCHAR(255) PRIMARY KEY,
    SellerName VARCHAR(255) NOT NULL,
    SellerNumber VARCHAR(20) NOT NULL,
    SellerLocation VARCHAR(255),
    SellerAddress VARCHAR(255),
    SellerUPIID VARCHAR(255) UNIQUE,
    SellerPassword VARCHAR(255) NOT NULL
);

CREATE TABLE Customer (
    CustomeremailID VARCHAR(255) PRIMARY KEY,
    CustomerName VARCHAR(255) NOT NULL,
    CustomerNumber VARCHAR(20) NOT NULL,
    CustomerLocation VARCHAR(255),
    CustomerAddress VARCHAR(255),
    CustomerPassword VARCHAR(255) NOT NULL
);

CREATE TABLE Product (
    SelleremailID VARCHAR(255),
    ProductName VARCHAR(255) NOT NULL,
    ProductDescription TEXT,
    ProductPrice DECIMAL(10, 2) NOT NULL,
    ProductImg BLOB NULL,
    SellerLocation VARCHAR(255),
    FOREIGN KEY (SelleremailID) REFERENCES Seller(SelleremailID)
);





