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
    active         BOOLEAN NOT NULL DEFAULT FALSE,
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
    id              SERIAL PRIMARY KEY,
    id_user         INTEGER REFERENCES "users" ON DELETE SET NULL ON UPDATE CASCADE,
    id_intervention INTEGER REFERENCES "intervention" ON DELETE CASCADE ON UPDATE CASCADE,
    vote            BOOLEAN NOT NULL,
    UNIQUE (id_user, id_intervention)
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
        INSERT INTO "notification"(type, id_intervention) VALUES ('comment', NEW.id) RETURNING id INTO notificationId;
        
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
INSERT INTO "users" (email, username, password, type, score, blocked, registry_date, active) VALUES (
    'jfcunha@fe.up.pt.LEIC.QA', 'admin', 
    '$2y$10$dNMxm/osVRRpGG1.bHkkuOrXa.tlehIQf/p44koFsso5B1qrn/2Py', 
    'Admin', NULL, NULL, '2021-11-01', TRUE
); -- Password: U2e_PZwP
INSERT INTO "users" (email, username, password, type, score, blocked, registry_date, active) VALUES (
    'percurso.academico@fe.up.pt.LEIC.QA', 'secretaria', 
    '$2y$10$IS6jxuz2SSw.NYskLKONuuINIqKoxwuPxixdYr6fmqaY5lQSkH.xq', 
    'Admin', NULL, NULL, '2021-11-01', TRUE
); -- Password: rh!6@S53

-- Teacher
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'mbb@fc.up.pt.LEIC.QA', 'mbb', 
    '$2y$10$y8NwYUvcvNwVb0YuaraMGu/KFzcRwUj8iwFOFp3SSIdS6SUWEd.em', 
    'Teacher', 'Manuel Bernardo Martins Barbosa', 'My research interests lie in Cryptography and Information Security and Formal Verification.', '2021-11-01', TRUE
); -- Password: #pX8-wMM
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jpleal@fc.up.pt.LEIC.QA', 'jpleal', 
    '$2y$10$GFYFISulYkFofjdWMiOVGOTytZDGii3/GI6e84gZPvRhZFiaQOqRy', 
    'Teacher', 'José Paulo de Vilhena Geraldes Leal', 'Para além de professor, interesso-me por escrever livros pedagógicos.', '2021-11-01', TRUE
); -- Password: HR?W25xG
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active, photo) VALUES (
    'ssn@fe.up.pt.LEIC.QA', 'ssn', 
    '$2y$10$NEqSfztbsCya3y9182wJn.fzw1CaPBOY2JhlGfbwvYELc9/Mr3z56', 
    'Teacher', 'Sérgio Sobral Nunes', 'I am an Assistant Professor at the Department of Informatics Engineering at the Faculty of Engineering of the University of Porto (FEUP), and a Senior Researcher at the Centre for Information Systems and Computer Graphics at INESC TEC.', '2021-11-01', TRUE, '5_1641229712_dl758KLoUU.png'
); -- Password: Z8K_?qjm
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'tbs@fe.up.pt.LEIC.QA', 'tbs', 
    '$2y$10$blELx.0yIxbREeej.54sDuQXyLcaP1yNtFQ1s9VB.NL/BOXvP1oU2', 
    'Teacher', 'Tiago Boldt Pereira de Sousa', 'Conclui o Mestrado em Mestrado Integrado em Engenharia Informática e Computação em 2011 pela Universidade do Porto Faculdade de Engenharia. Publiquei 5 artigos em revistas especializadas.', '2021-11-01', TRUE
); -- Password: Dfx3L$nA
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'amflorid@fc.up.pt.LEIC.QA', 'amflorid', 
    '$2y$10$feOxhu03znm2rTxjjMj6UeweSfLfrphvDphN2dtJktxfKD7l2xKl.', 
    'Teacher', 'António Mário da Silva Marcos Florido', 'Sou investigador e membro da direção do Laboratório de Inteligência Artificial e Ciência de Computadores (LIACC) da FCUP.', '2021-11-01', TRUE
); -- Password: dH&G2n2%
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'mricardo@fe.up.pt.LEIC.QA', 'mricardo', 
    '$2y$10$.C/dLep90O3hf34wkSWVLeO0.9CxF23zmaJNUolhM61hak1FtCgea', 
    'Teacher', 'Manuel Alberto Pereira Ricardo', 'Licenciado, Mestre e Doutor (2000) em Engenharia Eletrotécnica e de Computadores, ramo de Telecomunicações, pela Faculdade de Engenharia da Universidade do Porto (FEUP).', '2021-11-01', TRUE
); -- Password: M44&#q2C
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'pabranda@fc.up.pt.LEIC.QA', 'pabranda', 
    '$2y$10$dXotQ6O2M2L/AvHu4gVxNO4DQcfDmd0i2lusO2gUV250vuOZxx0sO', 
    'Teacher', 'Pedro Miguel Alves Brandão', 'Fiz o meu doutoramento no Computer Laboratory da Univ. de Cambridge sobre o tema de Body Sensor Networks. Obtive uma bolsa da Fundação para a Ciência e Tecnologia para suporte ao doutoramento.', '2021-11-01', TRUE
); -- Password: 9nDy&cjK

-- Student
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, photo) VALUES (
    'up201805455@fc.up.pt', 'up201805455', 
    '$2y$10$ZVT1VxJoxAsw3TbaRdyBbOyCz7WiNyIn6P1F.mXtLFd1LQ9fvigFC', 
    'Student', 'Alexandre Afonso', '2000-07-23 11:00:00', 2018, '2021-11-01', TRUE, '10_1641229580_vBEUWTuB0f.jpg'
); -- Password: hUdQ!Q6?
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, photo) VALUES (
    'up201906852@fe.up.pt', 'up201906852', 
    '$2y$10$b31tgmi3H4ba/VcRMtkPWO2FWRKZMEnySNt.1JNywFGwcjRyYjpCu', 
    'Student', 'Henrique Nunes', '2001-02-08 13:00:00', 2019, '2021-11-01', TRUE, '11_1641229484_onzioN1AGD.png'
); -- Password: @K4Agr6a
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active) VALUES (
    'up201905427@fe.up.pt', 'up201905427', 
    '$2y$10$L2L6LcnHcwpUfPZZHe1Nc.azp0pUuMlUmBGGiMd/4EkmEBAsd0kBm', 
    'Student', 'Patrícia Oliveira', '2001-03-19 17:00:00', 2019, '2021-11-01', TRUE
); -- Password: cL@Az7HY
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active) VALUES (
    'up201805327@fc.up.pt', 'up201805327', 
    '$2y$10$fyDBC/Y9xLDeHnAgmwg5PeHELVH5ZPqy2ErdvhMo7KuWJdHT0AbhO', 
    'Student', 'Tiago Antunes', '2000-06-10 11:00:00', 2018, '2021-11-01', TRUE
); -- Password: GKhg6j&T
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201905046@fe.up.pt.LEIC.QA', 'up201905046', 
    '$2y$10$zWwDbkWxuqAl.L.re.tOlu3HW1cSGM/7/SH2eJJt/kX9adb.Nwu8G', 
    'Student', 'Margarida Ribeiro', '2001-06-10 11:00:00', 2019, '2021-11-01', TRUE, TRUE, 'Abuso de permissões'
); -- Password: X_Bd9Nw2
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201476549@fc.up.pt.LEIC.QA', 'up201476549', 
    '$2y$10$gRjYE55mXurh8zpGN8.yCu5NVqjjmGhtSFQ7YYgv/T/fdq.AEXaIq', 
    'Student', 'Francisco Mendes', '1996-11-07 11:00:00', 2014, '2021-11-01', TRUE, TRUE, 'Conteúdos impróprios'
); -- Password: %L2mxp3V
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201823452@fe.up.pt.LEIC.QA', 'up201823452', 
    '$2y$10$4hUh29dBltUyWZPjhaR9Fe7oRdR2MdhDnx1SPXIyH9c4ZzVenLhJO', 
    'Student', 'Ana Martins', '2000-08-24 11:00:00', 2018, '2021-11-01', TRUE, TRUE, 'Conta foi hackeada'
); -- Password: H9V@Dvjh

-- More Teachers
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'lpreis@fe.up.pt.LEIC.QA', 'lpreis', 
    '$2y$10$TqSHAioLbGX8pQasq.ZBHeYpmzoho/G8J7K6ufZIlQAscygIUagH6', 
    'Teacher', 'Luís Paulo Gonçalves dos Reis', 'Licenciado (1993), Mestre (1995) e Doutor (2003) em Engenharia Eletrotécnica e de Computadores (especializações em Informática e Sistemas, Informática Industrial, Inteligência Artificial/Robótica) pela Universidade do Porto.', '2021-11-01', TRUE
); -- Password: q?88U7QF
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'pfs@fe.up.pt.LEIC.QA', 'pfs', 
    '$2y$10$3nM5pRrAmzXvwQm1Cm7YTutdvjnKXnu2renXDacqO/KyHLnFKWqIW', 
    'Teacher', 'Pedro Alexandre Guimarães Lobo Ferreira Souto', 'Professor Auxiliar, Departamento de Engenharia Informática.', '2021-11-01', TRUE
); -- Password: k53A!p4A
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jmpc@fe.up.pt.LEIC.QA', 'jmpc', 
    '$2y$10$zz1PARycK.6xIknR4I6tMOt2WBd8FR.5YStNWO0n1doAAXkN5Pb7i', 
    'Teacher', 'João Manuel Paiva Cardoso', 'Received a 5-year Electronics Engineering degree from the University of Aveiro in 1993. He has been involved in the organization of various international conferences.', '2021-11-01', TRUE
); -- Password: @q6QVmrZ
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'aaguiar@fe.up.pt.LEIC.QA', 'aaguiar', 
    '$2y$10$mpZimC.Nrap6QJgw5/Zd3ONWX1Q.h4AEje.fPk8j1xX2dvWLQFpl2', 
    'Teacher', 'Ademar Manuel Teixeira de Aguiar', 'Professor Associado na FEUP e investigador no INESC TEC, com mais de 30 anos de experiencia em desenvolvimento de software, especializou-se em arquitectura e design de software.', '2021-11-01', TRUE
); -- Password: vY&3Qu%U
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jpf@fe.up.pt.LEIC.QA', 'jpf', 
    '$2y$10$JoTPzlthNAR8XQUj1TZBfOWIjcAVJTOJ2CZ19un3xlVXfbRb.o0Ji', 
    'Teacher', 'João Carlos Pascoal Faria', 'Doutoramento em Engenharia Electrotécnica e de Computadores pela FEUP em 1999, onde é atualmente Professor Associado no Departamento de Engenharia Informática e Diretor do Mestrado Integrado em Engenharia Informática e Computação.', '2021-11-01', TRUE
); -- Password: NyXEB7#u
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'villate@fe.up.pt.LEIC.QA', 'jev', 
    '$2y$10$F87pRSDz1luNCs04ZX5gLe5dtJZdwJrsU72JPf88jxga6OIwHnab.', 
    'Teacher', 'Jaime Enrique Villate Matiz', 'Licenciatura em Física, 1983, Universidade Nacional de Colômbia, Bogotá. Licenciatura em Engenharia de Sistemas (Informática), 1984, Universidade Distrital de Bogotá, Colômbia. Master of  Arts em Física, 1987 e Ph. D. em Física, 1990...', '2021-11-01', TRUE
); -- Password: Q9&wdJkS
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jmcruz@fe.up.pt.LEIC.QA', 'mmc', 
    '$2y$10$jMgnjZFsJQgF7d.YY4Ks1OCBpty/LbyP//edm.rGbDzayKI15ejQ.', 
    'Teacher', 'José Manuel De Magalhães Cruz', 'Docente na FEUP', '2021-11-01', TRUE
); -- Password: _Pb7nXv2

-----------------------------------------
-- uc
-----------------------------------------

INSERT INTO "uc" (name, code, description) VALUES ('Fundamentos de Segurança Informática', 'FSI', 'Visa dotar os estudantes de uma visão abrangente dos aspetos de segurança inerentes ao desenvolvimento e operação de sistemas informáticos.');
INSERT INTO "uc" (name, code, description) VALUES ('Linguagens e Tecnologias Web', 'LTW', 'Desenvolve competências nas linguagens e tecnologias WEB, no contexto tecnológico atual, ou que foram determinantes no processo evolutivo da WEB.');
INSERT INTO "uc" (name, code, description) VALUES ('Laboratório de Bases de Dados e Aplicações Web', 'LBAW', 'Oferece uma perspetiva prática sobre duas áreas centrais da engenharia informática: bases de dados e linguagens e tecnologias web.');
INSERT INTO "uc" (name, code, description) VALUES ('Programação Funcional e em Lógica', 'PFL', 'Os paradigmas de Programação Funcional e de Programação em Lógica apresentam abordagens declarativas e baseadas em processos formais de raciocínio à programação.');
INSERT INTO "uc" (name, code, description) VALUES ('Redes de Computadores', 'RC', 'Introduz os estudantes no domínio de conhecimento das redes de comunicações: canais de comunicação e controlo da ligação de dados, modelos de erro e atraso...');
INSERT INTO "uc" (name, code, description) VALUES ('Compiladores', 'C', 'Fornecer os conceitos que permitam: compreender as fases de compilação de linguagens, em especial das linguagens imperativas e orientada por objectos; especificar a sintaxe...');
INSERT INTO "uc" (name, code, description) VALUES ('Computação Paralela e Distribuída', 'CPD', 'Introdução à computação paralela. Medidas de desempenho. Máquinas paralelas. Organização de memória e efeito da gestão da memória cache no desempenho do processador...');
INSERT INTO "uc" (name, code, description) VALUES ('Engenharia de Software', 'ES', 'Familiarizar-se com os métodos de engenharia e gestão necessários ao desenvolvimento de sistemas de software complexos e/ou em larga escala, de forma economicamente eficaz...');
INSERT INTO "uc" (name, code, description) VALUES ('Inteligência Artificial', 'IA', 'Esta unidade curricular apresenta um conjunto de assuntos nucleares para a área da Inteligência Artificial e dos Sistemas Inteligentes.');
INSERT INTO "uc" (name, code, description) VALUES ('Projeto Integrador', 'PI', 'Esta unidade curricular pretende expõr os estudantes a um projeto de Engenharia Informática, de domínio e escala reais.');
INSERT INTO "uc" (name, code, description) VALUES ('Sistemas Operativos', 'SO', 'Os objetivos principais desta unidade curricular são fornecer os conhecimentos fundamentais sobre: O1- a estrutura e o funcionamento de um sistema operativo genérico;...');
INSERT INTO "uc" (name, code, description) VALUES ('Física II', 'F II', 'Atualmente o processamento, armazenamento e transmissão de informação são feitos usando fenômenos eletromagnéticos. Consequentemente, a formação de base de um engenheiro informático...');

-----------------------------------------
-- teacher_uc
-----------------------------------------

INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (3, 1);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (4, 2);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (5, 3);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (6, 3);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (7, 4);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (8, 5);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (9, 6);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (9, 3);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (19, 6);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (18, 7);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (20, 8);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (21, 8);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (17, 9);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (5, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (6, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (17, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (18, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (19, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (20, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (21, 10);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (23, 11);
INSERT INTO "teacher_uc" (id_teacher, id_uc) VALUES (22, 12);

-----------------------------------------
-- follow_uc
-----------------------------------------

INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 2);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 3);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 4);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 5);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (10, 6);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 1);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 2);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 4);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 9);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 10);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (11, 12);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 2);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 4);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (12, 8);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (13, 3);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (13, 5);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (13, 8);
INSERT INTO "follow_uc" (id_student, id_uc) VALUES (13, 11);


-----------------------------------------
-- intervention
-----------------------------------------

-- question
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Como fazer um Wireframe?', 'Não sei que aplicação usar. Qual é a melhor e mais fácil de usar?', 3, '2021-11-04 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Sitemap:pode existir ligação componente-página?', 'É correto ligar uma página diretamente a uma componente de outra página?', 3, '2021-11-03 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Qual o nome do professor de LTW?', 'Não consigo entrar no sigarra e preciso mesmo de saber o nome do professor...', 2, '2021-11-06 13:00:00', 'question');

INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (10, 'Uma Promisse encontra-se sempre no estado pendente.', 'Na função update de comunicação com o servidor o servidor não retorna nenhum valor, porquê?', 2, '2021-12-20 9:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Qundo se deve utilizar PUT ou POST?', 'Não entendo a diferença entre PUT e POST, alguém explica melhor que a documentação?', 2, '2021-11-06 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (13, 'Que comandos são utilzados para alterar as configurações do switch?', 'Preciso de alterar as configruações de um switch par aa atividade laboratorial, mas a docuemntação é inexistente, alguém sabe como?', 5, '2021-12-06 16:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Quais são as consequências de não usarmos mecanismos de mitigação?', 'Quais são as consequências de não usarmos mecanismos de mitigação?', 1, '2021-11-14 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Como fazer um string format exploit?', 'Não entendo como se utiliza o %n nos exploits, principalmente quando o buffer tem limite de tamanho.', 1, '2021-11-26 13:00:00', 'question');


-- answer
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'O invision é o melhor embora não permita por textos por cima de caixas brancas. Há também o figma, mas é bastante mais complexo.', 1, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'Faz no papel e digitaliza, não há nada melhor.', 1, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'José Paulo Leal...', 3, '2021-11-06 13:23:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Nome: José Paulo de Vilhena Geraldes Leal, Email: jpleal@fc.up.pt', 3, '2021-11-06 13:59:00', 'answer');

INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'Há duas opções: ou o servidor está mal ou tens que fazer mais alguma coisa além da função update. Experimenta fazer correr em dois pcs com users diferentes para ver se o jogo já emparelha, aceitando a Promisse', 4, '2021-12-20 17:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Promise é um objeto usado para processamento assíncrono. Um Promise (de "promessa") representa um valor que pode estar disponível agora, no futuro ou nunca.', 4, '2021-12-20 18:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Usas o POST quando pretendes que algo no servidor altere o seu estado e que fazendo novamente o retorno poderia não ser o mesmo, O PUT usas em caso contrário.', 5, '2021-11-09 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'Ninguém sabe por isso vai experimentando e pode ser que dê.', 6, '2021-12-07 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Reflete nestes: enable, password:, configure terminal, vlan x0, end, show vlan id x0.', 6, '2021-12-08 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'Ficas sem clientes, pois não consegues dar garantias de confiança no teu sistema...', 7, '2021-11-15 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Primeiro tens de ter controlo sobre a string do printf e tem de ser algo do tipo: printf(input), para poderes considerar sequer uma vulnerabilidade...', 8, '2021-12-06 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'Usas uma sequência de %x para ver o endereço da stua string de input e depois %s ai, e tomas partido disso para acederes a endereços de outros sitios.', 8, '2021-12-20 18:30:00', 'answer');


-- comment
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Outra ferramenta que podes usar é o draw.io', 9, '2021-11-05 14:00:00', 'comment');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES ( 6, 'O figma é o melhor!', 9, '2021-11-05 14:21:00', 'comment');

-----------------------------------------
-- voting
-----------------------------------------

INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (10,  1, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6,  1, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (16,  2, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14,  2, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (12,  2, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (10,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (12,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (13,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (21,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (22,  3, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (12,  4, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18,  4, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6,  5, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 8,  5, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (21,  5, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6,  6, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (15,  6, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20,  7, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20,  8, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (12,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 8,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (19,  9, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11, 10, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6, 10, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (15, 10, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (14, 10, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 11, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (15, 11, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6, 12, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 8, 12, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (17, 12, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18, 13, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 13, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (21, 13, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (11, 14, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18, 14, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (18, 15, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (21, 16, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES ( 6, 16, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (15, 17, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 17, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 18, TRUE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 19, FALSE);
INSERT INTO "voting" (id_user, id_intervention, vote) VALUES (20, 20, TRUE);

-----------------------------------------
-- validation
-----------------------------------------

INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES ( 9, 6, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (11, 4, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (12, 4, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (13, 4, TRUE); 
INSERT INTO "validation" (id_answer, id_teacher, valid) VALUES (16, 8, FALSE); 

-----------------------------------------
-- notification
-----------------------------------------

INSERT INTO "notification" (date, type, status) VALUES ('2021-11-10 15:00:00', 'account_status', 'active');

-----------------------------------------
-- receive_not
-----------------------------------------

INSERT INTO "receive_not" (id_notification, id_user, read) VALUES (28, 9, FALSE);
