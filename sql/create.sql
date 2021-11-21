DROP SCHEMA IF EXISTS lbaw2185  CASCADE;
CREATE SCHEMA lbaw2185;

SET search_path TO lbaw2185;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE "tipo_utilizador" AS ENUM ('Administrador', 'Docente', 'Aluno');

CREATE TYPE "tipo_estado"     AS ENUM ('ativacao', 'bloqueio', 'eliminacao');

CREATE TYPE "tipo_validacao"  AS ENUM ('aceitacao', 'rejeicao');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE "utilizador" (
    id          SERIAL  PRIMARY KEY,
    email       TEXT    NOT NULL CONSTRAINT email_uk UNIQUE ,
    username    TEXT    NOT NULL CONSTRAINT username_uk UNIQUE,
    password    TEXT    NOT NULL,
    data_registo DATE   NOT NULL DEFAULT now(),
    nome        TEXT    NOT NULL,
    foto_perfil TEXT,
    sobre       TEXT,
    aniversario DATE,
    pontuacao   INTEGER NOT NULL DEFAULT 0,
    bloqueado   BOOLEAN DEFAULT FALSE,
    ano_ingresso INTEGER,
    TYPE tipo_estado NOT NULL,

    CONSTRAINT pontuacao_pos    CHECK (pontuacao >= 0),
    CONSTRAINT ano_ingresso_pos CHECK (ano_ingresso > 0)
);

create table "uc" (
    id      SERIAL PRIMARY KEY,
    nome    TEXT   NOT NULL CONSTRAINT nome_uk UNIQUE,
    sigla   TEXT   NOT NULL CONSTRAINT sigla_uk UNIQUE,
    descricao TEXT NOT NULL
);

create table "responsavel" (
    id_docente  INTEGER REFERENCES utilizador (id)  ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES uc (id)          ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_docente, id_uc)
);

create table "segue" (
    id_aluno    INTEGER REFERENCES utilizador (id)  ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES uc (id)          ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_aluno, id_uc)
);

CREATE TABLE "intervencao" (
    id          SERIAL  PRIMARY KEY,
    data        DATE    NOT NULL DEFAULT now(),
    pontuacao   INTEGER NOT NULL DEFAULT 0,
    autor       INTEGER REFERENCES utilizador (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT data_menor_hoje CHECK (data <= now())
);

CREATE TABLE "votacao" (
    id_utilizador   INTEGER REFERENCES utilizador (id)  ON DELETE SET NULL ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao (id) ON DELETE CASCADE  ON UPDATE CASCADE,
    voto            BOOLEAN NOT NULL,
    PRIMARY KEY (id_utilizador, id_intervencao)
);

CREATE TABLE "questao" (
    id_intervencao  INTEGER PRIMARY KEY REFERENCES intervencao (id)         ON DELETE CASCADE  ON UPDATE CASCADE,
    titulo          TEXT    NOT NULL,
    categoria       INTEGER NOT NULL    REFERENCES questao (id_intervencao) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE "resposta" (
    id_intervencao  INTEGER PRIMARY KEY REFERENCES intervencao (id)         ON DELETE CASCADE ON UPDATE CASCADE,
    id_questao      INTEGER NOT NULL    REFERENCES questao (id_intervencao) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE "comentario" (
    id_intervencao  INTEGER PRIMARY KEY REFERENCES intervencao (id)          ON DELETE CASCADE ON UPDATE CASCADE,
    id_resposta     INTEGER NOT NULL    REFERENCES resposta (id_intervencao) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE "validacao" (
    id_resposta INTEGER PRIMARY KEY REFERENCES resposta (id_intervencao) ON DELETE CASCADE  ON UPDATE CASCADE,
    id_docente  INTEGER REFERENCES utilizador (id) ON DELETE SET NULL ON UPDATE CASCADE, 
    valida      BOOLEAN NOT NULL
);

CREATE TABLE "notificacao" (
    id      SERIAL PRIMARY KEY,
    data    DATE NOT NULL DEFAULT now(),
    CONSTRAINT data_menor_hoje CHECK (data <= now())
);

CREATE TABLE "recebe" (
    id_notificacao  INTEGER REFERENCES notificacao (id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_utilizador   INTEGER REFERENCES utilizador (id)  ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_notificacao, id_utilizador)
);

CREATE TABLE "razao" (
    id      SERIAL  PRIMARY KEY,
    razao   TEXT    NOT NULL CONSTRAINT razao_uk UNIQUE
);

CREATE TABLE "estado_conta_not" (
    id_notificacao  INTEGER PRIMARY KEY REFERENCES notificacao (id) ON DELETE CASCADE  ON UPDATE CASCADE,
    id_razao        INTEGER REFERENCES razao (id) ON DELETE RESTRICT ON UPDATE CASCADE, 
    TYPE tipo_estado NOT NULL
);

CREATE TABLE "comentario_not" (
    id_notificacao  INTEGER PRIMARY KEY REFERENCES notificacao (id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_comentario   INTEGER REFERENCES comentario (id_intervencao)  ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE "resposta_not" (
    id_notificacao  INTEGER PRIMARY KEY REFERENCES notificacao (id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_resposta     INTEGER REFERENCES resposta (id_intervencao)    ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE "questao_not" (
    id_notificacao  INTEGER PRIMARY KEY REFERENCES notificacao (id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_questao      INTEGER REFERENCES questao (id_intervencao)     ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE "validacao_not" (
    id_notificacao  INTEGER PRIMARY KEY REFERENCES notificacao (id) ON DELETE CASCADE ON UPDATE CASCADE,
    id_resposta     INTEGER REFERENCES resposta (id_intervencao)    ON DELETE CASCADE ON UPDATE CASCADE, 
    TYPE tipo_validacao NOT NULL
);
