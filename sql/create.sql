DROP SCHEMA IF EXISTS lbaw2185  CASCADE;
CREATE SCHEMA lbaw2185;

SET search_path TO lbaw2185;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE "tipo_utilizador" AS ENUM ('Administrador', 'Docente', 'Aluno');

CREATE TYPE "tipo_intervencao" AS ENUM ('questao', 'resposta', 'comentario');

CREATE TYPE "tipo_notificacao" AS ENUM ('questao', 'resposta', 'comentario', 'validacao', 'denuncia', 'estado_conta');

CREATE TYPE "tipo_estado" AS ENUM ('ativacao', 'bloqueio', 'eliminacao');

CREATE TYPE "tipo_validacao" AS ENUM ('aceitacao', 'rejeicao');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE "utilizador" (
    id            SERIAL PRIMARY KEY,
    email         TEXT NOT NULL CONSTRAINT email_uk UNIQUE ,
    username      TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
    password      TEXT NOT NULL,
    data_registo  DATE NOT NULL DEFAULT now(),
    nome          TEXT,
    foto_perfil   TEXT,
    sobre         TEXT,
    data_nascimento DATE,
    pontuacao     INTEGER DEFAULT 0,
    bloqueado     BOOLEAN DEFAULT FALSE,
    ano_ingresso  INTEGER,
    TYPE tipo_estado NOT NULL,

    CONSTRAINT nome_NN CHECK ((tipo_estado == 'Administrador' AND nome NULL) OR (tipo_estado != 'Administrador')),
    CONSTRAINT pontuacao_NN CHECK ((tipo_estado == 'Administrador' AND pontuacao NULL) OR (tipo_estado != 'Administrador')),
    CONSTRAINT pontuacao_pos CHECK (pontuacao >= 0),
    CONSTRAINT ano_ingresso_pos CHECK (ano_ingresso > 0),
    CONSTRAINT ano_ingresso_NN CHECK ((tipo_estado == 'Aluno' AND ano_ingresso NOT NULL) OR (tipo_estado != 'Aluno'))
);

create table "uc" (
    id        SERIAL PRIMARY KEY,
    nome      TEXT NOT NULL CONSTRAINT nome_uk UNIQUE,
    sigla     TEXT NOT NULL CONSTRAINT sigla_uk UNIQUE,
    descricao TEXT NOT NULL
);

create table "docente_uc" (
    id_docente  INTEGER REFERENCES utilizador ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES uc ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_docente, id_uc)
);

create table "segue_uc" (
    id_aluno    INTEGER REFERENCES utilizador ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES uc ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_aluno, id_uc)
);

CREATE TABLE "intervencao" (
    id              SERIAL PRIMARY KEY,
    data            DATE NOT NULL DEFAULT now(),
    texto           TEXT NOT NULL,
    pontuacao       INTEGER NOT NULL DEFAULT 0,
    autor           INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE,
    titulo          TEXT
    categoria       INTEGER REFERENCES uc ON DELETE CASCADE ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    TYPE tipo_intervencao NOT NULL,

    CONSTRAINT data_menor_agora   CHECK (data <= now()),
    CONSTRAINT id_intervencao_NN  CHECK ((tipo_intervencao != 'questao' AND id_intervencao NOT NULL) OR (tipo_intervencao == 'questao')),
    CONSTRAINT titulo_categ_NN    CHECK ((tipo_intervencao == 'questao' AND titulo NOT NULL AND categoria NOT NULL) OR (tipo_intervencao != 'questao'))
);

CREATE TABLE "votacao" (
    id_utilizador   INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE  ON UPDATE CASCADE,
    voto            BOOLEAN NOT NULL,
    PRIMARY KEY (id_utilizador, id_intervencao)
);

CREATE TABLE "validacao" (
    id_resposta INTEGER PRIMARY KEY REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    id_docente  INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE, 
    valida      BOOLEAN NOT NULL
);


CREATE TABLE "notificacao" (
    id              SERIAL PRIMARY KEY,
    data            DATE NOT NULL DEFAULT now(),
    id_razao        INTEGER REFERENCES razao ON DELETE RESTRICT ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    TYPE tipo_estado,
    TYPE tipo_validacao,
    TYPE tipo_notificacao NOT NULL,

    CONSTRAINT data_menor_agora CHECK (data <= now()),
    CONSTRAINT razao_estado_NN  CHECK ((tipo_notificacao == 'estado_conta' AND id_razao NOT NULL AND tipo_estado NOT NULL) OR (tipo_notificacao != 'estado_conta')),
    CONSTRAINT validacao_NN     CHECK ((tipo_notificacao == 'validacao' AND tipo_validacao NOT NULL) OR (tipo_notificacao != 'validacao')),
    CONSTRAINT intervencao_NN   CHECK ((tipo_notificacao != 'estado_conta' AND id_intervencao NOT NULL) OR (tipo_notificacao == 'estado_conta'))
);

CREATE TABLE "razao" (
    id      SERIAL PRIMARY KEY,
    razao   TEXT NOT NULL CONSTRAINT razao_uk UNIQUE
);

CREATE TABLE "recebe" (
    id_notificacao  INTEGER REFERENCES notificacao ON DELETE CASCADE ON UPDATE CASCADE,
    id_utilizador   INTEGER REFERENCES utilizador ON DELETE CASCADE ON UPDATE CASCADE,
    lida            BOOLEAN NOT NULL,
    PRIMARY KEY (id_notificacao, id_utilizador)
);