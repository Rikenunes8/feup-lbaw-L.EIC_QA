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

CREATE TABLE "uc" (
    id        SERIAL PRIMARY KEY,
    nome      TEXT NOT NULL CONSTRAINT nome_uk UNIQUE,
    sigla     TEXT NOT NULL CONSTRAINT sigla_uk UNIQUE,
    descricao TEXT NOT NULL
);

CREATE TABLE "docente_uc" (
    id_docente  INTEGER REFERENCES utilizador ON DELETE CASCADE ON UPDATE CASCADE,
    id_uc       INTEGER REFERENCES uc ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (id_docente, id_uc)
);

CREATE TABLE "segue_uc" (
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

-----------------------------------------
-- Indexes
-----------------------------------------

CREATE INDEX intervencao_superior ON intervencao USING hash (id_intervencao);

CREATE INDEX autor_intervencao ON intervencao USING hash (id_autor);

CREATE INDEX data_notificacao ON notificacao USING btree (data);

-- FTS Index

ALTER TABLE intervencao ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION intervencao_procura() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('portuguese', NEW.titulo), 'A') ||
            setweight(to_tsvector('portuguese', NEW.texto), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.titulo <> OLD.titulo OR NEW.texto <> OLD.texto) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('portuguese', NEW.titulo), 'A') ||
                setweight(to_tsvector('portuguese', NEW.texto), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER intervencao_procura
BEFORE INSERT OR UPDATE ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE intervencao_procura();

CREATE INDEX procura_idx ON intervencao USING GIN (tsvectors);

-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

-- TRIGGER01
CREATE FUNCTION proibir_votar_propria_intervencao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.id_utilizador = (SELECT id_autor FROM intervencao WHERE NEW.id_intervencao=id) THEN
        RAISE EXCEPTION 'Um utilizador não pode votar nas suas próprias intervenções';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER proibir_votar_propria_intervencao
BEFORE INSERT ON votacao
FOR EACH ROW
EXECUTE PROCEDURE proibir_votar_propria_intervencao();

-- TRIGGER02
CREATE FUNCTION data_maior_intervencao_superior() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.data <= (SELECT data FROM intervencao WHERE NEW.id_intervencao=id) THEN
        RAISE EXCEPTION 'Uma resposta/comentário não pode ter data inferior à sua respetiva intervenção de ordem superior';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER data_maior_intervencao_superior
BEFORE INSERT OR UPDATE ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE data_maior_intervencao_superior();

-- TRIGGER03
CREATE FUNCTION incr_pontuacao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.voto=TRUE THEN
    UPDATE intervencao SET pontuacao=pontuacao+1 WHERE id=NEW.id_intervencao;
    ELSE
    UPDATE intervencao SET pontuacao=pontuacao-1 WHERE id=NEW.id_intervencao;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER incr_pontuacao
AFTER INSERT ON votacao
FOR EACH ROW
EXECUTE PROCEDURE incr_pontuacao();

-- TRIGGER04
CREATE FUNCTION update_pontuacao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.voto=TRUE AND OLD.voto=FALSE THEN
    UPDATE intervencao SET pontuacao=pontuacao+2 WHERE id=NEW.id_intervencao;
    ELSEIF NEW.voto=FALSE AND OLD.voto=TRUE THEN
    UPDATE intervencao SET pontuacao=pontuacao-2 WHERE id=NEW.id_intervencao;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_pontuacao
AFTER UPDATE ON votacao
FOR EACH ROW
EXECUTE PROCEDURE update_pontuacao();


-- TRIGGER05
CREATE FUNCTION verificar_docente_uc() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Docente' <> (SELECT tipo FROM utilizador WHERE id=NEW.id_docente) THEN
        RAISE EXCEPTION 'Só podem ser associados a uma uc utilizadores do tipo docente';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verificar_docente_uc
BEFORE INSERT ON docente_uc
FOR EACH ROW
EXECUTE PROCEDURE verificar_docente_uc();

-- TRIGGER06
CREATE FUNCTION verificar_segue_uc() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Aluno' <> (SELECT tipo FROM utilizador WHERE id=NEW.id_aluno) THEN
        RAISE EXCEPTION 'Só utilizadores do tipo aluno é que podem seguir uma uc';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verificar_segue_uc
BEFORE INSERT ON segue_uc
FOR EACH ROW
EXECUTE PROCEDURE verificar_segue_uc();

-- TRIGGER07
CREATE FUNCTION verificar_autor_intervencao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Administrador' = (SELECT tipo FROM utilizador WHERE id=NEW.id_autor) THEN
        RAISE EXCEPTION 'Administradores não podem ser autores de intervenções';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verificar_autor_intervencao
BEFORE INSERT ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE verificar_autor_intervencao();

-- TRIGGER08
CREATE FUNCTION verificar_votacao_intervencao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Administrador' = (SELECT tipo FROM utilizador WHERE id=NEW.id_utilizador) THEN
        RAISE EXCEPTION 'Administradores não podem votar em nenhuma intervenção';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verificar_votacao_intervencao
BEFORE INSERT ON votacao
FOR EACH ROW
EXECUTE PROCEDURE verificar_votacao_intervencao();

-- TRIGGER09
CREATE FUNCTION verificar_validacao_intervencao() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'resposta' <> (SELECT tipo FROM intervencao WHERE id=NEW.id_resposta) THEN
        RAISE EXCEPTION 'Só podem ser validadas intervenções do tipo resposta';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER verificar_validacao_intervencao
BEFORE INSERT ON validacao
FOR EACH ROW
EXECUTE PROCEDURE verificar_validacao_intervencao();

-- TRIGGER10

-- TRIGGER11

-- TRIGGER12
CREATE FUNCTION gerar_notificacao_questao() RETURNS TRIGGER AS
$BODY$
DECLARE
utilizador BIGINT;
notificacaoId BIGINT;
BEGIN
    IF NEW.tipo = 'questao' THEN
        INSERT INTO notificacao(tipo, id_intervencao) VALUES ('questao', NEW.id) RETURNING id INTO notificacaoId;
        
        FOR utilizador IN (SELECT id_aluno FROM segue_uc WHERE id_uc=NEW.categoria) LOOP 
            INSERT INTO recebe_not(id_notificacao, id_utilizador, lida) VALUES (notificacaoId, utilizador, FALSE);
        END LOOP;

        FOR utilizador IN (SELECT id_docente FROM docente_uc WHERE id_uc=NEW.categoria) LOOP 
            INSERT INTO recebe_not(id_notificacao, id_utilizador, lida) VALUES (notificacaoId, utilizador, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER gerar_notificacao_questao
AFTER INSERT ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE gerar_notificacao_questao();

-- TRIGGER13
CREATE FUNCTION gerar_notificacao_resposta() RETURNS TRIGGER AS
$BODY$
DECLARE
autor BIGINT;
notificacaoId BIGINT;
BEGIN
    IF NEW.tipo = 'resposta' THEN
        INSERT INTO notificacao(tipo, id_intervencao) VALUES ('resposta', NEW.id) RETURNING id INTO notificacaoId;
        
        FOR autor IN (SELECT id_autor FROM intervencao WHERE id=NEW.id_intervencao) LOOP
            INSERT INTO recebe_not(id_notificacao, id_utilizador, lida) VALUES (notificacaoId, autor, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER gerar_notificacao_resposta
AFTER INSERT ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE gerar_notificacao_resposta();

-- TRIGGER14
CREATE FUNCTION gerar_notificacao_comentario() RETURNS TRIGGER AS
$BODY$
DECLARE
autor BIGINT;
notificacaoId BIGINT;
BEGIN
    IF NEW.tipo = 'comentario' THEN
        INSERT INTO notificacao(tipo, id_intervencao) VALUES ('comentario', NEW.id) RETURNING id INTO notificacaoId;
        
        FOR autor IN (SELECT id_autor FROM intervencao WHERE id=NEW.id_intervencao) LOOP
            INSERT INTO recebe_not(id_notificacao, id_utilizador, lida) VALUES (notificacaoId, autor, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER gerar_notificacao_comentario
AFTER INSERT ON intervencao
FOR EACH ROW
EXECUTE PROCEDURE gerar_notificacao_comentario();

-- TRIGGER15

-- TRIGGER16

-- TRIGGER17

-- TRIGGER18

-----------------------------------------
-- TRANSACTIONS
-----------------------------------------

BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT COUNT(*)
FROM intervencao
WHERE tipo='questao';

SELECT titulo, categoria, pontuacao, data, (SELECT COUNT(*) FROM validacao
                                            WHERE id_resposta IN 
                                                (SELECT id FROM intervencao WHERE id_intervencao=I.id) 
                                                AND valida = TRUE) AS n_respostas_validadas
FROM intervencao AS I
WHERE tipo='questao';

END TRANSACTION;
