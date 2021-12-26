create schema if not exists lbaw21;

DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS cards CASCADE;
DROP TABLE IF EXISTS items CASCADE;
DROP TABLE IF EXISTS uc CASCADE;
DROP TABLE IF EXISTS teacher_uc CASCADE;
DROP TABLE IF EXISTS follow_uc CASCADE;

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  email VARCHAR UNIQUE NOT NULL,
  password VARCHAR NOT NULL,
  remember_token VARCHAR
);

CREATE TABLE cards (
  id SERIAL PRIMARY KEY,
  name VARCHAR NOT NULL,
  user_id INTEGER REFERENCES users NOT NULL
);

CREATE TABLE items (
  id SERIAL PRIMARY KEY,
  card_id INTEGER NOT NULL REFERENCES cards ON DELETE CASCADE,
  description VARCHAR NOT NULL,
  done BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE "uc" (
    id          SERIAL PRIMARY KEY,
    name        TEXT NOT NULL CONSTRAINT name_uk UNIQUE,
    code        TEXT NOT NULL CONSTRAINT code_uk UNIQUE,
    description TEXT NOT NULL
);

CREATE TABLE "teacher_uc" (
    id_teacher  INTEGER REFERENCES "users" ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES "uc" ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_teacher, id_uc)
);

CREATE TABLE "follow_uc" (
    id_student  INTEGER REFERENCES "users" ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES "uc" ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_student, id_uc)
);

INSERT INTO users VALUES (
  DEFAULT,
  'John Doe',
  'admin@example.com',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')

INSERT INTO users VALUES (
  2,
  'Manuel Bernardo Martins Barbosa',
  'mbb@fc.up.pt',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')

INSERT INTO users VALUES (
  3,
  'José Paulo de Vilhena Geraldes Leal',
  'jpleal@fc.up.pt',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')

INSERT INTO users VALUES (
  4,
  'Sérgio Sobral Nunes',
  'ssn@fe.up.pt',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')

INSERT INTO users VALUES (
  5,
  'Tiago Boldt Pereira de Sousa',
  'tbs@fe.up.pt',
  '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W'
); -- Password is 1234. Generated using Hash::make('1234')


INSERT INTO cards VALUES (DEFAULT, 'Things to do', 1);
INSERT INTO items VALUES (DEFAULT, 1, 'Buy milk');
INSERT INTO items VALUES (DEFAULT, 1, 'Walk the dog', true);

INSERT INTO cards VALUES (DEFAULT, 'Things not to do', 1);
INSERT INTO items VALUES (DEFAULT, 2, 'Break a leg');
INSERT INTO items VALUES (DEFAULT, 2, 'Crash the car');


INSERT INTO "uc" (id, name, code, description) VALUES (0, 'Fundamentos de Segurança Informática', 'FSI', 'Visa dotar os estudantes de uma visão abrangente dos aspetos de segurança inerentes ao desenvolvimento e operação de sistemas informáticos.');
INSERT INTO "uc" (id, name, code, description) VALUES (1, 'Linguagens e Tecnologias Web', 'LTW', 'Desenvolve competências nas linguagens e tecnologias WEB, no contexto tecnológico atual, ou que foram determinantes no processo evolutivo da WEB.');
INSERT INTO "uc" (id, name, code, description) VALUES (2, 'Laboratório de Bases de Dados e Aplicações Web', 'LBAW', 'Oferece uma perspetiva prática sobre duas áreas centrais da engenharia informática: bases de dados e linguagens e tecnologias web.');

INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (2, 0);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (3, 1);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (4, 2);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (5, 2);

INSERT INTO "follow_uc" (id_student, id_uc) VALUES (2, 2);
