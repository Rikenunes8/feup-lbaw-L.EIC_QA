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
    tipo tipo_utilizador NOT NULL,

    CONSTRAINT nome_NN         CHECK ((tipo='Administrador' AND nome IS NULL) OR (tipo<>'Administrador' AND nome IS NOT NULL)),
    CONSTRAINT foto_perfil_NN  CHECK ((tipo='Administrador' AND foto_perfil IS NULL) OR (tipo<>'Administrador')),
    CONSTRAINT sobre_NN        CHECK ((tipo='Administrador' AND sobre IS NULL) OR (tipo<>'Administrador')),
    CONSTRAINT data_nasc_NN    CHECK ((tipo='Administrador' AND data_nascimento IS NULL) OR (tipo<>'Administrador')),
    CONSTRAINT bloqueado_NN    CHECK ((tipo='Administrador' AND bloqueado IS NULL) OR (tipo<>'Administrador' AND bloqueado IS NOT NULL)),
    CONSTRAINT pontuacao_NN    CHECK ((tipo='Administrador' AND pontuacao IS NULL) OR (tipo<>'Administrador' AND pontuacao IS NOT NULL AND pontuacao >= 0)),
    CONSTRAINT ano_ingresso_NN CHECK ((tipo<>'Aluno' AND ano_ingresso IS NULL) OR (tipo='Aluno' AND ano_ingresso IS NOT NULL AND ano_ingresso > 0))
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
    id_autor        INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE,
    texto           TEXT NOT NULL,
    data            DATE NOT NULL DEFAULT now(),
    pontuacao       INTEGER NOT NULL DEFAULT 0,
    titulo          TEXT,
    categoria       INTEGER REFERENCES uc ON DELETE CASCADE ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    tipo tipo_intervencao NOT NULL,

    CONSTRAINT data_menor_agora   CHECK (data <= now()),
    CONSTRAINT titulo_categ_NN    CHECK ((tipo ='questao' AND titulo IS NOT NULL AND categoria IS NOT NULL) OR (tipo<>'questao' AND titulo IS NULL AND categoria IS NULL)),
    CONSTRAINT id_intervencao_NN  CHECK ((tipo<>'questao' AND id_intervencao IS NOT NULL) OR (tipo='questao' AND id_intervencao IS NULL))
);

CREATE TABLE "votacao" (
    id_utilizador   INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    voto            BOOLEAN NOT NULL,
    PRIMARY KEY (id_utilizador, id_intervencao)
);

CREATE TABLE "validacao" (
    id_resposta INTEGER PRIMARY KEY REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    id_docente  INTEGER REFERENCES utilizador ON DELETE SET NULL ON UPDATE CASCADE, 
    valida      BOOLEAN NOT NULL
);


CREATE TABLE "razao_bloqueio" (
    id      SERIAL PRIMARY KEY,
    razao   TEXT NOT NULL CONSTRAINT razao_uk UNIQUE
);

CREATE TABLE "notificacao" (
    id              SERIAL PRIMARY KEY,
    data            DATE NOT NULL DEFAULT now(),
    id_razao        INTEGER REFERENCES razao_bloqueio ON DELETE RESTRICT ON UPDATE CASCADE,
    id_intervencao  INTEGER REFERENCES intervencao ON DELETE CASCADE ON UPDATE CASCADE,
    estado tipo_estado,
    validacao tipo_validacao,
    tipo tipo_notificacao NOT NULL,

    CONSTRAINT data_menor_agora CHECK (data <= now()),
    CONSTRAINT razao_estado_NN  CHECK ((tipo='estado_conta' AND id_razao IS NOT NULL AND estado IS NOT NULL) OR (tipo<>'estado_conta' AND id_razao IS NULL AND estado IS NULL)),
    CONSTRAINT intervencao_NN   CHECK ((tipo<>'estado_conta' AND id_intervencao IS NOT NULL) OR (tipo='estado_conta' AND id_intervencao IS NULL)),
    CONSTRAINT validacao_NN     CHECK ((tipo='validacao' AND validacao IS NOT NULL) OR (tipo<>'validacao' AND validacao IS NULL))
);

CREATE TABLE "recebe_not" (
    id_notificacao  INTEGER REFERENCES notificacao ON DELETE CASCADE ON UPDATE CASCADE,
    id_utilizador   INTEGER REFERENCES utilizador ON DELETE CASCADE ON UPDATE CASCADE,
    lida            BOOLEAN NOT NULL,
    PRIMARY KEY (id_notificacao, id_utilizador)
);
