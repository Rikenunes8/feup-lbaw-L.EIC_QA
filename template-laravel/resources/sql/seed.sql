DROP SCHEMA IF EXISTS lbaw2185  CASCADE;
CREATE SCHEMA lbaw2185;

SET search_path TO lbaw2185;

-----------------------------------------
-- Types
-----------------------------------------

CREATE TYPE "type_user" AS ENUM ('Admin', 'Teacher', 'Student');

CREATE TYPE "type_intervention" AS ENUM ('question', 'answer', 'comment');

CREATE TYPE "type_notification" AS ENUM ('question', 'answer', 'comment', 'validation', 'report', 'account_status');

CREATE TYPE "type_status" AS ENUM ('active', 'block', 'delete');

CREATE TYPE "type_validation" AS ENUM ('acceptance', 'rejection');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE "users" (
    id             SERIAL PRIMARY KEY,
    email           TEXT NOT NULL CONSTRAINT email_uk UNIQUE ,
    username       TEXT NOT NULL CONSTRAINT username_uk UNIQUE,
    password       TEXT NOT NULL,
    registry_date  TIMESTAMP  NOT NULL DEFAULT now(),
    name           TEXT,
    photo          TEXT,
    about          TEXT,
    birthdate      TIMESTAMP ,
    score          INTEGER DEFAULT 0,
    blocked        BOOLEAN DEFAULT FALSE,
    block_reason   TEXT,
    entry_year     INTEGER,
    type type_user NOT NULL,

    CONSTRAINT name_NN          CHECK ((type='Admin' AND name IS NULL) OR (type<>'Admin' AND name IS NOT NULL)),
    CONSTRAINT photo_NN         CHECK ((type='Admin' AND photo IS NULL) OR (type<>'Admin')),
    CONSTRAINT about_NN         CHECK ((type='Admin' AND about IS NULL) OR (type<>'Admin')),
    CONSTRAINT birthdate_NN     CHECK ((type='Admin' AND birthdate IS NULL) OR (type<>'Admin')),
    CONSTRAINT blocked_NN       CHECK ((type='Admin' AND blocked IS NULL) OR (type<>'Admin' AND blocked IS NOT NULL)),
    CONSTRAINT score_NN         CHECK ((type='Admin' AND score IS NULL) OR (type<>'Admin' AND score IS NOT NULL)),
    CONSTRAINT block_reason_NN  CHECK (((blocked IS NULL OR NOT blocked) AND block_reason IS NULL) OR (blocked AND block_reason IS NOT NULL)),
    CONSTRAINT entry_year_NN    CHECK ((type<>'Student' AND entry_year IS NULL) OR (type='Student' AND entry_year IS NOT NULL AND entry_year > 0))
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

CREATE TABLE "intervention" (
    id              SERIAL PRIMARY KEY,
    id_author       INTEGER REFERENCES "users" ON DELETE SET NULL ON UPDATE CASCADE,
    text            TEXT NOT NULL,
    date            TIMESTAMP NOT NULL DEFAULT now(),
    votes           INTEGER NOT NULL DEFAULT 0,
    title           TEXT,
    category        INTEGER REFERENCES "uc" ON DELETE CASCADE ON UPDATE CASCADE,
    id_intervention INTEGER REFERENCES "intervention" ON DELETE CASCADE ON UPDATE CASCADE,
    type type_intervention NOT NULL,

    CONSTRAINT date_smaller_now   CHECK (date <= now()),
    CONSTRAINT title_categ_NN     CHECK ((type ='question' AND title IS NOT NULL AND category IS NOT NULL) OR (type<>'question' AND title IS NULL AND category IS NULL)),
    CONSTRAINT id_intervention_NN CHECK ((type<>'question' AND id_intervention IS NOT NULL) OR (type='question' AND id_intervention IS NULL))
);

CREATE TABLE "voting" (
    id_user         INTEGER REFERENCES "users" ON DELETE SET NULL ON UPDATE CASCADE,
    id_intervention INTEGER REFERENCES "intervention" ON DELETE CASCADE ON UPDATE CASCADE,
    vote            BOOLEAN NOT NULL,
    PRIMARY KEY (id_user, id_intervention)
);

CREATE TABLE "validation" (
    id_answer   INTEGER PRIMARY KEY REFERENCES "intervention" ON DELETE CASCADE ON UPDATE CASCADE,
    id_teacher  INTEGER REFERENCES "users" ON DELETE SET NULL ON UPDATE CASCADE, 
    valid       BOOLEAN NOT NULL
);

CREATE TABLE "notification" (
    id              SERIAL PRIMARY KEY,
    date            TIMESTAMP  NOT NULL DEFAULT now(),
    id_intervention INTEGER REFERENCES "intervention" ON DELETE CASCADE ON UPDATE CASCADE,
    status     type_status,
    validation type_validation,
    type       type_notification NOT NULL,

    CONSTRAINT date_smaller_now CHECK (date <= now()),
    CONSTRAINT intervention_NN  CHECK ((type<>'account_status' AND id_intervention IS NOT NULL) OR (type='account_status' AND id_intervention IS NULL)),
    CONSTRAINT status_NN        CHECK ((type='account_status' AND status IS NOT NULL) OR (type<>'account_status' AND status IS NULL)),
    CONSTRAINT validation_NN    CHECK ((type='validation' AND validation IS NOT NULL) OR (type<>'validation' AND validation IS NULL))
);

CREATE TABLE "receive_not" (
    id_notification INTEGER REFERENCES "notification" ON DELETE CASCADE ON UPDATE CASCADE,
    id_user         INTEGER REFERENCES "users" ON DELETE CASCADE ON UPDATE CASCADE,
    read            BOOLEAN NOT NULL,
    PRIMARY KEY (id_notification, id_user)
);

-----------------------------------------
-- Indexes
-----------------------------------------

CREATE INDEX intervention_superior ON "intervention" USING hash (id_intervention);

CREATE INDEX author_intervention ON "intervention" USING hash (id_author);

CREATE INDEX date_notification ON "notification" USING btree (date);

-- FTS Index

ALTER TABLE intervention ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION intervention_search() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('portuguese', NEW.title), 'A') ||
            setweight(to_tsvector('portuguese', NEW.text), 'B')
        );
    END IF;
    IF TG_OP = 'UPDATE' THEN
        IF (NEW.title <> OLD.title OR NEW.text <> OLD.text) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('portuguese', NEW.title), 'A') ||
                setweight(to_tsvector('portuguese', NEW.text), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER intervention_search
BEFORE INSERT OR UPDATE ON intervention
FOR EACH ROW
EXECUTE PROCEDURE intervention_search();

CREATE INDEX search_idx ON intervention USING GIN (tsvectors);

-----------------------------------------
-- TRIGGERS and UDFs
-----------------------------------------

-- TRIGGER01
CREATE FUNCTION forbid_vote_own_intervention() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.id_user = (SELECT id_author FROM "intervention" WHERE NEW.id_intervention=id) THEN
        RAISE EXCEPTION 'A user cannot vote on their own interventions';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER forbid_vote_own_intervention
BEFORE INSERT ON "voting"
FOR EACH ROW
EXECUTE PROCEDURE forbid_vote_own_intervention();

-- TRIGGER02
CREATE FUNCTION date_bigger_intervention_superior() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.date <= (SELECT date FROM "intervention" WHERE NEW.id_intervention=id) THEN
        RAISE EXCEPTION 'An answer/comment cannot have a date lower than its respective intervention of an higher order';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER date_bigger_intervention_superior
BEFORE INSERT OR UPDATE ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE date_bigger_intervention_superior();

-- TRIGGER03
CREATE FUNCTION incr_votes() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'comment'=(SELECT type FROM "intervention" WHERE id=NEW.id_intervention) THEN
      RAISE EXCEPTION 'A comment cannot be voted on';
    ELSEIF NEW.vote=TRUE THEN
      UPDATE "intervention" SET votes=votes+1 WHERE id=NEW.id_intervention;
      UPDATE "users" SET score=score+1 WHERE id=(SELECT id_author FROM "intervention" AS I WHERE I.id=NEW.id_intervention);
    ELSE
      UPDATE "intervention" SET votes=votes-1 WHERE id=NEW.id_intervention;
      UPDATE "users" SET score=score-1 WHERE id=(SELECT id_author FROM "intervention" AS I WHERE I.id=NEW.id_intervention);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER incr_votes
AFTER INSERT ON "voting"
FOR EACH ROW
EXECUTE PROCEDURE incr_votes();

-- TRIGGER04
CREATE FUNCTION update_votes() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.vote=TRUE AND OLD.vote=FALSE THEN
      UPDATE "intervention" SET votes=votes+2 WHERE id=NEW.id_intervention;
      UPDATE "users" SET score=score+2 WHERE id=(SELECT id_author FROM "intervention" AS I WHERE I.id=NEW.id_intervention);
    ELSEIF NEW.vote=FALSE AND OLD.vote=TRUE THEN
      UPDATE "intervention" SET votes=votes-2 WHERE id=NEW.id_intervention;
      UPDATE "users" SET score=score-2 WHERE id=(SELECT id_author FROM "intervention" AS I WHERE I.id=NEW.id_intervention);
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER update_votes
AFTER UPDATE OF vote ON "voting"
FOR EACH ROW
EXECUTE PROCEDURE update_votes();

-- TRIGGER05
CREATE FUNCTION check_teacher_uc() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Teacher' <> (SELECT type FROM "users" WHERE id=NEW.id_teacher) THEN
        RAISE EXCEPTION 'Only users of the type Teacher can be associated with a uc';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_teacher_uc
BEFORE INSERT ON "teacher_uc"
FOR EACH ROW
EXECUTE PROCEDURE check_teacher_uc();

-- TRIGGER06
CREATE FUNCTION check_follow_uc() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Student' <> (SELECT type FROM "users" WHERE id=NEW.id_student) THEN
        RAISE EXCEPTION 'Only users of the type Student can follow a uc';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_follow_uc
BEFORE INSERT ON "follow_uc"
FOR EACH ROW
EXECUTE PROCEDURE check_follow_uc();

-- TRIGGER07
CREATE FUNCTION check_author_intervention() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Admin' = (SELECT type FROM "users" WHERE id=NEW.id_author) THEN
        RAISE EXCEPTION 'Administrators cannot be authors of interventions';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_author_intervention
BEFORE INSERT ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE check_author_intervention();

-- TRIGGER08
CREATE FUNCTION check_vote_intervention() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF 'Admin' = (SELECT type FROM "users" WHERE id=NEW.id_user) THEN
        RAISE EXCEPTION 'Administrators cannot vote on any intervention';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_vote_intervention
BEFORE INSERT ON "voting"
FOR EACH ROW
EXECUTE PROCEDURE check_vote_intervention();

-- TRIGGER09
CREATE FUNCTION check_validation_intervention() RETURNS TRIGGER AS
$BODY$
BEGIN 
    IF 'answer' <> (SELECT type FROM "intervention" WHERE id=NEW.id_answer) THEN
        RAISE EXCEPTION 'Only response type interventions can be validated';
    END IF;
    IF NEW.id_teacher NOT IN (SELECT DUC.id_teacher
                              FROM ("intervention" I1 INNER JOIN "intervention" I2 ON I1.id_intervention = I2.id) INNER JOIN "teacher_uc" DUC ON I2.category = DUC.id_uc
                              WHERE I1.id = NEW.id_answer) THEN
        RAISE EXCEPTION 'Only teachers of the answer category can validate it';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_validation_intervention
BEFORE INSERT ON "validation"
FOR EACH ROW
EXECUTE PROCEDURE check_validation_intervention();

-- TRIGGER10
CREATE FUNCTION check_association_interventions() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF NEW.type = 'answer' AND 'question' <> (SELECT type FROM "intervention" WHERE id=NEW.id_intervention) THEN
        RAISE EXCEPTION 'An intervention of the type "answer" must be associated with an intervention of the type "question"';
    ELSEIF NEW.type = 'comment' AND 'answer' <> (SELECT type FROM "intervention" WHERE id=NEW.id_intervention) THEN
        RAISE EXCEPTION 'An intervention of the type "comment" must be associated with an intervention of the type "answer"';
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER check_association_interventions
BEFORE INSERT ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE check_association_interventions();

-- TRIGGER11
CREATE FUNCTION generate_notification_question() RETURNS TRIGGER AS
$BODY$
DECLARE
userId BIGINT;
notificationId BIGINT;
BEGIN
    IF NEW.type = 'question' THEN
        INSERT INTO "notification"(type, id_intervention) VALUES ('question', NEW.id) RETURNING id INTO notificationId;
        
        FOR userId IN (SELECT id_student FROM "follow_uc" WHERE id_uc=NEW.category) LOOP 
            INSERT INTO "receive_not"(id_notification, id_user, read) VALUES (notificationId, userId, FALSE);
        END LOOP;

        FOR userId IN (SELECT id_teacher FROM "teacher_uc" WHERE id_uc=NEW.category) LOOP 
            INSERT INTO "receive_not"(id_notification, id_user, read) VALUES (notificationId, userId, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_question
AFTER INSERT ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_question();

-- TRIGGER12
CREATE FUNCTION generate_notification_answer() RETURNS TRIGGER AS
$BODY$
DECLARE
author BIGINT;
notificationId BIGINT;
BEGIN
    IF NEW.type = 'answer' THEN
        INSERT INTO "notification"(type, id_intervention) VALUES ('answer', NEW.id) RETURNING id INTO notificationId;
        
        FOR author IN (SELECT id_author FROM "intervention" WHERE id=NEW.id_intervention) LOOP
            INSERT INTO "receive_not"(id_notification, id_user, read) VALUES (notificationId, author, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_answer
AFTER INSERT ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_answer();

-- TRIGGER13
CREATE FUNCTION generate_notification_comment() RETURNS TRIGGER AS
$BODY$
DECLARE
author BIGINT;
notificationId BIGINT;
BEGIN
    IF NEW.type = 'comment' THEN
        INSERT INTO notification(type, id_intervention) VALUES ('comment', NEW.id) RETURNING id INTO notificationId;
        
        FOR author IN (SELECT id_author FROM "intervention" WHERE id=NEW.id_intervention) LOOP
            INSERT INTO "receive_not"(id_notification, id_user, read) VALUES (notificationId, author, FALSE);
        END LOOP;
    END IF;
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_comment
AFTER INSERT ON "intervention"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_comment();

-- TRIGGER14
CREATE FUNCTION generate_notification_validation() RETURNS TRIGGER AS
$BODY$
DECLARE
author BIGINT;
notificationId BIGINT;
BEGIN
    IF NEW.valid = TRUE THEN
        INSERT INTO "notification"(type, id_intervention, validation) VALUES ('validation', NEW.id_answer, 'acceptance') RETURNING id INTO notificationId;
    ELSE
        INSERT INTO "notification"(type, id_intervention, validation) VALUES ('validation', NEW.id_answer, 'rejection') RETURNING id INTO notificationId;
    END IF;

    FOR author IN (SELECT id_author FROM intervention WHERE id=NEW.id_answer) LOOP
        INSERT INTO "receive_not"(id_notification, id_user, read) VALUES (notificationId, author, FALSE);
    END LOOP;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_validation
AFTER INSERT OR UPDATE OF valid ON "validation"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_validation();

-----------------------------------------
-- TRANSACTIONS
-----------------------------------------
/*
BEGIN TRANSACTION;

SET TRANSACTION ISOLATION LEVEL SERIALIZABLE READ ONLY;

SELECT COUNT(*)
FROM "intervention"
WHERE type='question';

SELECT title, category, votes, date, (SELECT COUNT(*) FROM "validation"
                                            WHERE id_answer IN 
                                                (SELECT id FROM "intervention" WHERE id_intervention=I.id) 
                                                AND valid = TRUE) AS n_valid_answers
FROM "intervention" AS I
WHERE type='question';

END TRANSACTION;
*/
-----------------------------------------
-- user
-----------------------------------------

-- Admin
INSERT INTO "users" (id, email, username, password, type, score, blocked, registry_date) VALUES (0, 'jfcunha@fe.up.pt', 'admin', 'admin', 'Admin', NULL, NULL, '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, score, blocked, registry_date) VALUES (1, 'percurso.academico@fe.up.pt', 'secretaria', 'p-academico', 'Admin', NULL, NULL, '2021-11-01');

-- Teacher
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (2, 'mbb@fc.up.pt', 'mbb', 'mbb', 'Teacher', 'Manuel Bernardo Martins Barbosa', 'My research interests lie in Cryptography and Information Security and Formal Verification.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (3, 'jpleal@fc.up.pt', 'jpleal', 'jpleal', 'Teacher', 'José Paulo de Vilhena Geraldes Leal', 'Para além de professor, interesso-me por escrever livros pedagógicos.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (4, 'ssn@fe.up.pt', 'ssn', 'ssn', 'Teacher', 'Sérgio Sobral Nunes', 'I am an Assistant Professor at the Department of Informatics Engineering at the Faculty of Engineering of the University of Porto (FEUP), and a Senior Researcher at the Centre for Information Systems and Computer Graphics at INESC TEC.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (5, 'tbs@fe.up.pt', 'tbs', 'tbs', 'Teacher', 'Tiago Boldt Pereira de Sousa', 'Conclui o Mestrado em Mestrado Integrado em Engenharia Informática e Computação em 2011 pela Universidade do Porto Faculdade de Engenharia. Publiquei 5 artigos em revistas especializadas.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (6, 'amflorid@fc.up.pt', 'amflorid', 'amflorid', 'Teacher', 'António Mário da Silva Marcos Florido', 'Sou investigador e membro da direção do Laboratório de Inteligência Artificial e Ciência de Computadores (LIACC) da FCUP.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (7, 'mricardo@fe.up.pt', 'mricardo', 'mricardo', 'Teacher', 'Manuel Alberto Pereira Ricardo', 'Licenciado, Mestre e Doutor (2000) em Engenharia Eletrotécnica e de Computadores, ramo de Telecomunicações, pela Faculdade de Engenharia da Universidade do Porto (FEUP).', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (8, 'pabranda@fc.up.pt', 'pabranda', 'pabranda', 'Teacher', 'Pedro Miguel Alves Brandão', 'Fiz o meu doutoramento no Computer Laboratory da Univ. de Cambridge sobre o tema de Body Sensor Networks. Obtive uma bolsa da Fundação para a Ciência e Tecnologia para suporte ao doutoramento.', '2021-11-01');

INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (16, 'lpreis@fe.up.pt', 'lpreis', 'lpreis', 'Teacher', 'Luís Paulo Gonçalves dos Reis', 'Licenciado (1993), Mestre (1995) e Doutor (2003) em Engenharia Eletrotécnica e de Computadores (especializações em Informática e Sistemas, Informática Industrial, Inteligência Artificial/Robótica) pela Universidade do Porto.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (17, 'pfs@fe.up.pt', 'pfs', 'pfs', 'Teacher', 'Pedro Alexandre Guimarães Lobo Ferreira Souto', 'Professor Auxiliar, Departamento de Engenharia Informática.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (18, 'jmpc@fe.up.pt', 'jmpc', 'jmpc', 'Teacher', 'João Manuel Paiva Cardoso', 'Received a 5-year Electronics Engineering degree from the University of Aveiro in 1993. He has been involved in the organization of various international conferences.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (19, 'aaguiar@fe.up.pt', 'aaguiar', 'aaguiar', 'Teacher', 'Ademar Manuel Teixeira de Aguiar', 'Professor Associado na FEUP e investigador no INESC TEC, com mais de 30 anos de experiencia em desenvolvimento de software, especializou-se em arquitectura e design de software.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (20, 'jpf@fe.up.pt', 'jpf', 'jpf', 'Teacher', 'João Carlos Pascoal Faria', 'Doutoramento em Engenharia Electrotécnica e de Computadores pela FEUP em 1999, onde é atualmente Professor Associado no Departamento de Engenharia Informática e Diretor do Mestrado Integrado em Engenharia Informática e Computação.', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (21, 'villate@fe.up.pt', 'jev', 'jev', 'Teacher', 'Jaime Enrique Villate Matiz', 'Licenciatura em Física, 1983, Universidade Nacional de Colômbia, Bogotá. Licenciatura em Engenharia de Sistemas (Informática), 1984, Universidade Distrital de Bogotá, Colômbia. Master of  Arts em Física, 1987 e Ph. D. em Física, 1990...', '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, about, registry_date) VALUES (22, 'jmcruz@fe.up.pt', 'mmc', 'mmc', 'Teacher', 'José Manuel De Magalhães Cruz', 'Docente na FEUP', '2021-11-01');

-- Student
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date) VALUES ( 9, 'up201805455@fc.up.pt', 'up201805455', 'up201805455', 'Student', 'Alexandre Afonso', '2000-07-23 11:00:00', 2018, '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (10, 'up201906852@fe.up.pt', 'up201906852', 'up201906852', 'Student', 'Henrique Nunes', '2001-02-08 13:00:00', 2019, '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (11, 'up201905427@fe.up.pt', 'up201905427', 'up201905427', 'Student', 'Patrícia Oliveira', '2001-03-19 17:00:00', 2019, '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (12, 'up201805327@fc.up.pt', 'up201805327', 'up201805327', 'Student', 'Tiago Antunes', '2000-06-10 11:00:00', 2018, '2021-11-01');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (13, 'up201905046@fe.up.pt', 'up201905046', 'up201905046', 'Student', 'Margarida Ribeiro', '2001-06-10 11:00:00', 2019, '2021-11-01', TRUE, 'Abuso de permissões');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (14, 'up201476549@fc.up.pt', 'up201476549', 'up201476549', 'Student', 'Francisco Mendes', '1996-11-07 11:00:00', 2014, '2021-11-01', TRUE, 'Conteúdos impróprios');
INSERT INTO "users" (id, email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (15, 'up201823452@fe.up.pt', 'up201823452', 'up201823452', 'Student', 'Ana Martins', '2000-08-24 11:00:00', 2018, '2021-11-01', TRUE, 'Conta foi hackeada');

-- $2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W
-- -- Password is 1234. Generated using Hash::make('1234')

UPDATE "users" SET password = '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W';

-----------------------------------------
-- uc
-----------------------------------------

INSERT INTO "uc" (id, name, code, description) VALUES (0, 'Fundamentos de Segurança Informática', 'FSI', 'Visa dotar os estudantes de uma visão abrangente dos aspetos de segurança inerentes ao desenvolvimento e operação de sistemas informáticos.');
INSERT INTO "uc" (id, name, code, description) VALUES (1, 'Linguagens e Tecnologias Web', 'LTW', 'Desenvolve competências nas linguagens e tecnologias WEB, no contexto tecnológico atual, ou que foram determinantes no processo evolutivo da WEB.');
INSERT INTO "uc" (id, name, code, description) VALUES (2, 'Laboratório de Bases de Dados e Aplicações Web', 'LBAW', 'Oferece uma perspetiva prática sobre duas áreas centrais da engenharia informática: bases de dados e linguagens e tecnologias web.');
INSERT INTO "uc" (id, name, code, description) VALUES (3, 'Programação Funcional e em Lógica', 'PFL', 'Os paradigmas de Programação Funcional e de Programação em Lógica apresentam abordagens declarativas e baseadas em processos formais de raciocínio à programação.');
INSERT INTO "uc" (id, name, code, description) VALUES (4, 'Redes de Computadores', 'RC', 'Introduz os estudantes no domínio de conhecimento das redes de comunicações: canais de comunicação e controlo da ligação de dados, modelos de erro e atraso...');
INSERT INTO "uc" (id, name, code, description) VALUES (5, 'Compiladores', 'C', 'Fornecer os conceitos que permitam: compreender as fases de compilação de linguagens, em especial das linguagens imperativas e orientada por objectos; especificar a sintaxe...');
INSERT INTO "uc" (id, name, code, description) VALUES (6, 'Computação Paralela e Distribuída', 'CPD', 'Introdução à computação paralela. Medidas de desempenho. Máquinas paralelas. Organização de memória e efeito da gestão da memória cache no desempenho do processador...');
INSERT INTO "uc" (id, name, code, description) VALUES (7, 'Engenharia de Software', 'ES', 'Familiarizar-se com os métodos de engenharia e gestão necessários ao desenvolvimento de sistemas de software complexos e/ou em larga escala, de forma economicamente eficaz...');
INSERT INTO "uc" (id, name, code, description) VALUES (8, 'Inteligência Artificial', 'IA', 'Esta unidade curricular apresenta um conjunto de assuntos nucleares para a área da Inteligência Artificial e dos Sistemas Inteligentes.');
INSERT INTO "uc" (id, name, code, description) VALUES (9, 'Projeto Integrador', 'PI', 'Esta unidade curricular pretende expõr os estudantes a um projeto de Engenharia Informática, de domínio e escala reais.');
INSERT INTO "uc" (id, name, code, description) VALUES (10, 'Sistemas Operativos', 'SO', 'Os objetivos principais desta unidade curricular são fornecer os conhecimentos fundamentais sobre: O1- a estrutura e o funcionamento de um sistema operativo genérico;...');
INSERT INTO "uc" (id, name, code, description) VALUES (11, 'Física II', 'F II', 'Atualmente o processamento, armazenamento e transmissão de informação são feitos usando fenômenos eletromagnéticos. Consequentemente, a formação de base de um engenheiro informático...');

-----------------------------------------
-- teacher_uc
-----------------------------------------

INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (2, 0);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (3, 1);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (4, 2);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (5, 2);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (6, 3);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (7, 4);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (8, 4);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (8, 2);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (18, 5);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (17, 6);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (19, 7);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (20, 7);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (16, 8);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (4, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (5, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (16, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (17, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (18, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (19, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (20, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (22, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (21, 11);

-----------------------------------------
-- follow_uc
-----------------------------------------

INSERT INTO "follow_uc" (id_student, id_uc) VALUES (9, 1);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (9, 2);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (9, 3);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (9, 4);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (9, 5);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 0);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 1);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 3);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 8);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 9);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 11);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 1);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 3);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 7);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 2);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 4);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 7);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 10);


-----------------------------------------
-- intervention
-----------------------------------------

-- question
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (0, 10, 'Como fazer um Wireframe?', 'Não sei que aplicação usar. Qual é a melhor e mais fácil de usar?', 2, '2021-11-04 13:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (1, 10, 'Sitemap:pode existir ligação componente-página?', 'É correto ligar uma página diretamente a uma componente de outra página?', 2, '2021-11-03 13:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (2, 10, 'Qual o nome do professor de LTW?', 'Não consigo entrar no sigarra e preciso mesmo de saber o nome do professor...', 1, '2021-11-06 13:00:00', 'question');

INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (3,  9, 'Uma Promisse encontra-se sempre no estado pendente.', 'Na função update de comunicação com o servidor o servidor não retorna nenhum valor, porquê?', 1, '2021-12-20 9:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (4, 11, 'Qundo se deve utilizar PUT ou POST?', 'Não entendo a diferença entre PUT e POST, alguém explica melhor que a documentação?', 1, '2021-11-06 13:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (5, 12, 'Que comandos são utilzados para alterar as configurações do switch?', 'Preciso de alterar as configruações de um switch par aa atividade laboratorial, mas a docuemntação é inexistente, alguém sabe como?', 4, '2021-12-06 16:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (6, 11, 'Quais são as consequências de não usarmos mecanismos de mitigação?', 'Quais são as consequências de não usarmos mecanismos de mitigação?', 0, '2021-11-14 13:00:00', 'question');
INSERT INTO "intervention" (id, id_author, title, text, category, date, type) VALUES (7, 11, 'Como fazer um string format exploit?', 'Não entendo como se utiliza o %n nos exploits, principalmente quando o buffer tem limite de tamanho.', 0, '2021-11-26 13:00:00', 'question');


-- answer
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES ( 8,  9, 'O invision é o melhor embora não permita por textos por cima de caixas brancas. Há também o figma, mas é bastante mais complexo.', 0, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES ( 9, 12, 'Faz no papel e digitaliza, não há nada melhor.', 0, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (10, 12, 'José Paulo Leal...', 2, '2021-11-06 13:23:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (11,  9, 'Nome: José Paulo de Vilhena Geraldes Leal, Email: jpleal@fc.up.pt', 2, '2021-11-06 13:59:00', 'answer');

INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (12, 10, 'Há duas opções: ou o servidor está mal ou tens que fazer mais alguma coisa além da função update. Experimenta fazer correr em dois pcs com users diferentes para ver se o jogo já emparelha, aceitando a Promisse', 3, '2021-12-20 17:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (13, 11, 'Promise é um objeto usado para processamento assíncrono. Um Promise (de "promessa") representa um valor que pode estar disponível agora, no futuro ou nunca.', 3, '2021-12-20 18:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (14,  9, 'Usas o POST quando pretendes que algo no servidor altere o seu estado e que fazendo novamente o retorno poderia não ser o mesmo, O PUT usas em caso contrário.', 4, '2021-11-09 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (15, 10, 'Ninguém sabe por isso vai experimentando e pode ser que dê.', 5, '2021-12-07 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (16, 11, 'Reflete nestes: enable, password:, configure terminal, vlan x0, end, show vlan id x0.', 5, '2021-12-08 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (17, 10, 'Ficas sem clientes, pois não consegues dar garantias de confiança no teu sistema...', 6, '2021-11-15 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (18,  9, 'Primeiro tens de ter controlo sobre a string do printf e tem de ser algo do tipo: printf(input), para poderes considerar sequer uma vulnerabilidade...', 7, '2021-12-06 13:00:00', 'answer');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (19, 12, 'Usas uma sequência de %x para ver o endereço da stua string de input e depois %s ai, e tomas partido disso para acederes a endereços de outros sitios.', 7, '2021-12-20 18:30:00', 'answer');


-- comment
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (20, 11, 'Outra ferramenta que podes usar é o draw.io', 8, '2021-11-05 14:00:00', 'comment');
INSERT INTO "intervention" (id, id_author, text, id_intervention, date, type) VALUES (21,  5, 'O figma é o melhor!', 8, '2021-11-05 14:21:00', 'comment');

-----------------------------------------
-- voting
-----------------------------------------

INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 9,  0, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5,  0, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (15,  1, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (13,  1, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11,  1, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 9,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (12,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (21,  2, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11,  3, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5,  4, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 7,  4, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20,  4, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5,  5, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14,  5, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19,  6, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19,  7, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 7,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (16,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (10,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5,  9, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14,  9, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (13,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 10, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14, 10, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5, 11, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 7, 11, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (16, 11, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17, 12, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 12, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 12, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (10, 13, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17, 13, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17, 14, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 15, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 5, 15, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14, 16, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 16, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 17, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 18, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19, 19, TRUE);

-----------------------------------------
-- validation
-----------------------------------------

INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES ( 8, 5, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (10, 3, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (11, 3, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (12, 3, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (15, 7, FALSE); 

-----------------------------------------
-- notification
-----------------------------------------

INSERT INTO "notification" (id, date, type, status) VALUES (0, '2021-11-10 15:00:00', 'account_status', 'active');

-----------------------------------------
-- receive_not
-----------------------------------------

INSERT INTO "receive_not" (id_notification, id_user, read) VALUES (0, 8, FALSE);
