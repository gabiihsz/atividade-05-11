create database longa_vida;



create table plano
 (
  numero int primary key not null,
  descricao varchar(30),
  valor decimal(10,2)
  );
  
create table cliente
 (
 plano int not null,
 nome char(40) primary key not null,
 Endereco char(35),
 cidade char(20),
 estado char(2),
 cep char (9),
 FOREIGN KEY (plano) REFERENCES plano(numero)
  ); 
  