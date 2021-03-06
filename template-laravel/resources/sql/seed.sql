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
    receive_email  BOOLEAN NOT NULL DEFAULT FALSE,
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
    edit_date       TIMESTAMP,
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
    id_user         INTEGER REFERENCES "users" ON DELETE CASCADE ON UPDATE CASCADE,
    status     type_status,
    validation type_validation,
    type       type_notification NOT NULL,

    CONSTRAINT date_smaller_now CHECK (date <= now()),
    CONSTRAINT intervention_NN  CHECK ((type<>'account_status' AND id_intervention IS NOT NULL) OR (type='account_status' AND id_intervention IS NULL)),
    CONSTRAINT user_NN          CHECK (((type<>'account_status' AND type<>'report') AND id_user IS NULL) OR ((type='account_status' OR type='report') AND id_user IS NOT NULL)),
    CONSTRAINT status_NN        CHECK ((type='account_status' AND status IS NOT NULL) OR (type<>'account_status' AND status IS NULL)),
    CONSTRAINT validation_NN    CHECK ((type='validation' AND validation IS NOT NULL) OR (type<>'validation' AND validation IS NULL))
);

CREATE TABLE "receive_not" (
    id_notification INTEGER REFERENCES "notification" ON DELETE CASCADE ON UPDATE CASCADE,
    id_user         INTEGER REFERENCES "users" ON DELETE CASCADE ON UPDATE CASCADE,
    read            BOOLEAN NOT NULL DEFAULT FALSE,
    to_email        BOOLEAN NOT NULL DEFAULT TRUE,
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
            INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, userId);
        END LOOP;

        FOR userId IN (SELECT id_teacher FROM "teacher_uc" WHERE id_uc=NEW.category) LOOP 
            INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, userId);
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
            INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, author);
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
            INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, author);
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

    FOR author IN (SELECT id_author FROM "intervention" WHERE id=NEW.id_answer) LOOP
        INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, author);
    END LOOP;

    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_validation
AFTER INSERT OR UPDATE OF valid ON "validation"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_validation();

-- TRIGGER15
CREATE FUNCTION generate_notification_account_status() RETURNS TRIGGER AS
$BODY$
DECLARE
notificationId BIGINT;
BEGIN
    IF NEW.blocked = TRUE THEN
        INSERT INTO "notification"(type, id_user, status) VALUES ('account_status', NEW.id, 'block') RETURNING id INTO notificationId;
    ELSE
        INSERT INTO "notification"(type, id_user, status) VALUES ('account_status', NEW.id, 'active') RETURNING id INTO notificationId;
    END IF;

    INSERT INTO "receive_not"(id_notification, id_user) VALUES (notificationId, NEW.id);
    RETURN NEW;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER generate_notification_account_status
AFTER UPDATE OF blocked ON "users"
FOR EACH ROW
EXECUTE PROCEDURE generate_notification_account_status();

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
    'jfcunha@fe.up.qa.pt', 'admin', 
    '$2y$10$dNMxm/osVRRpGG1.bHkkuOrXa.tlehIQf/p44koFsso5B1qrn/2Py', 
    'Admin', NULL, NULL, '2021-11-01', TRUE
); -- Password: U2e_PZwP
INSERT INTO "users" (email, username, password, type, score, blocked, registry_date, active) VALUES (
    'percurso.academico@fe.up.qa.pt', 'secretaria', 
    '$2y$10$IS6jxuz2SSw.NYskLKONuuINIqKoxwuPxixdYr6fmqaY5lQSkH.xq', 
    'Admin', NULL, NULL, '2021-11-01', TRUE
); -- Password: rh!6@S53

-- Teacher
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'mbb@fc.up.qa.pt', 'mbb', 
    '$2y$10$y8NwYUvcvNwVb0YuaraMGu/KFzcRwUj8iwFOFp3SSIdS6SUWEd.em', 
    'Teacher', 'Manuel Bernardo Martins Barbosa', 'My research interests lie in Cryptography and Information Security and Formal Verification.', '2021-11-01', TRUE
); -- Password: #pX8-wMM
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jpleal@fc.up.qa.pt', 'jpleal', 
    '$2y$10$GFYFISulYkFofjdWMiOVGOTytZDGii3/GI6e84gZPvRhZFiaQOqRy', 
    'Teacher', 'Jos?? Paulo de Vilhena Geraldes Leal', 'Para al??m de professor, interesso-me por escrever livros pedag??gicos.', '2021-11-01', TRUE
); -- Password: HR?W25xG
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active, photo) VALUES (
    'ssn@fe.up.qa.pt', 'ssn', 
    '$2y$10$NEqSfztbsCya3y9182wJn.fzw1CaPBOY2JhlGfbwvYELc9/Mr3z56', 
    'Teacher', 'S??rgio Sobral Nunes', 'I am an Assistant Professor at the Department of Informatics Engineering at the Faculty of Engineering of the University of Porto (FEUP), and a Senior Researcher at the Centre for Information Systems and Computer Graphics at INESC TEC.', '2021-11-01', TRUE, '5_1641229712_dl758KLoUU.png'
); -- Password: Z8K_?qjm
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'tbs@fe.up.qa.pt', 'tbs', 
    '$2y$10$blELx.0yIxbREeej.54sDuQXyLcaP1yNtFQ1s9VB.NL/BOXvP1oU2', 
    'Teacher', 'Tiago Boldt Pereira de Sousa', 'Conclui o Mestrado em Mestrado Integrado em Engenharia Inform??tica e Computa????o em 2011 pela Universidade do Porto Faculdade de Engenharia. Publiquei 5 artigos em revistas especializadas.', '2021-11-01', TRUE
); -- Password: Dfx3L$nA
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'amflorid@fc.up.qa.pt', 'amflorid', 
    '$2y$10$feOxhu03znm2rTxjjMj6UeweSfLfrphvDphN2dtJktxfKD7l2xKl.', 
    'Teacher', 'Ant??nio M??rio da Silva Marcos Florido', 'Sou investigador e membro da dire????o do Laborat??rio de Intelig??ncia Artificial e Ci??ncia de Computadores (LIACC) da FCUP.', '2021-11-01', TRUE
); -- Password: dH&G2n2%
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'mricardo@fe.up.qa.pt', 'mricardo', 
    '$2y$10$.C/dLep90O3hf34wkSWVLeO0.9CxF23zmaJNUolhM61hak1FtCgea', 
    'Teacher', 'Manuel Alberto Pereira Ricardo', 'Licenciado, Mestre e Doutor (2000) em Engenharia Eletrot??cnica e de Computadores, ramo de Telecomunica????es, pela Faculdade de Engenharia da Universidade do Porto (FEUP).', '2021-11-01', TRUE
); -- Password: M44&#q2C
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'pabranda@fc.up.qa.pt', 'pabranda', 
    '$2y$10$dXotQ6O2M2L/AvHu4gVxNO4DQcfDmd0i2lusO2gUV250vuOZxx0sO', 
    'Teacher', 'Pedro Miguel Alves Brand??o', 'Fiz o meu doutoramento no Computer Laboratory da Univ. de Cambridge sobre o tema de Body Sensor Networks. Obtive uma bolsa da Funda????o para a Ci??ncia e Tecnologia para suporte ao doutoramento.', '2021-11-01', TRUE
); -- Password: 9nDy&cjK

-- Student
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, photo) VALUES (
    'up201805455@fc.up.qa.pt', 'up201805455', 
    '$2y$10$ZVT1VxJoxAsw3TbaRdyBbOyCz7WiNyIn6P1F.mXtLFd1LQ9fvigFC', 
    'Student', 'Alexandre Afonso', '2000-07-23 11:00:00', 2018, '2021-11-01', TRUE, '10_1641229580_vBEUWTuB0f.jpg'
); -- Password: hUdQ!Q6?
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, photo) VALUES (
    'up201906852@fe.up.qa.pt', 'up201906852', 
    '$2y$10$b31tgmi3H4ba/VcRMtkPWO2FWRKZMEnySNt.1JNywFGwcjRyYjpCu', 
    'Student', 'Henrique Nunes', '2001-02-08 13:00:00', 2019, '2021-11-01', TRUE, '11_1641229484_onzioN1AGD.png'
); -- Password: @K4Agr6a
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active) VALUES (
    'up201905427@fe.up.qa.pt', 'up201905427', 
    '$2y$10$L2L6LcnHcwpUfPZZHe1Nc.azp0pUuMlUmBGGiMd/4EkmEBAsd0kBm', 
    'Student', 'Patr??cia Oliveira', '2001-03-19 17:00:00', 2019, '2021-11-01', TRUE
); -- Password: cL@Az7HY
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active) VALUES (
    'up201805327@fc.up.qa.pt', 'up201805327', 
    '$2y$10$fyDBC/Y9xLDeHnAgmwg5PeHELVH5ZPqy2ErdvhMo7KuWJdHT0AbhO', 
    'Student', 'Tiago Antunes', '2000-06-10 11:00:00', 2018, '2021-11-01', TRUE
); -- Password: GKhg6j&T
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201905046@fe.up.qa.pt', 'up201905046', 
    '$2y$10$zWwDbkWxuqAl.L.re.tOlu3HW1cSGM/7/SH2eJJt/kX9adb.Nwu8G', 
    'Student', 'Margarida Ribeiro', '2001-06-10 11:00:00', 2019, '2021-11-01', TRUE, TRUE, 'Abuso de permiss??es'
); -- Password: X_Bd9Nw2
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201476549@fc.up.qa.pt', 'up201476549', 
    '$2y$10$gRjYE55mXurh8zpGN8.yCu5NVqjjmGhtSFQ7YYgv/T/fdq.AEXaIq', 
    'Student', 'Francisco Mendes', '1996-11-07 11:00:00', 2014, '2021-11-01', TRUE, TRUE, 'Conte??dos impr??prios'
); -- Password: %L2mxp3V
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, active, blocked, block_reason) VALUES (
    'up201823452@fe.up.qa.pt', 'up201823452', 
    '$2y$10$4hUh29dBltUyWZPjhaR9Fe7oRdR2MdhDnx1SPXIyH9c4ZzVenLhJO', 
    'Student', 'Ana Martins', '2000-08-24 11:00:00', 2018, '2021-11-01', TRUE, TRUE, 'Conta foi hackeada'
); -- Password: H9V@Dvjh

-- More Teachers
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'lpreis@fe.up.qa.pt', 'lpreis', 
    '$2y$10$TqSHAioLbGX8pQasq.ZBHeYpmzoho/G8J7K6ufZIlQAscygIUagH6', 
    'Teacher', 'Lu??s Paulo Gon??alves dos Reis', 'Licenciado (1993), Mestre (1995) e Doutor (2003) em Engenharia Eletrot??cnica e de Computadores (especializa????es em Inform??tica e Sistemas, Inform??tica Industrial, Intelig??ncia Artificial/Rob??tica) pela Universidade do Porto.', '2021-11-01', TRUE
); -- Password: q?88U7QF
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'pfs@fe.up.qa.pt', 'pfs', 
    '$2y$10$3nM5pRrAmzXvwQm1Cm7YTutdvjnKXnu2renXDacqO/KyHLnFKWqIW', 
    'Teacher', 'Pedro Alexandre Guimar??es Lobo Ferreira Souto', 'Professor Auxiliar, Departamento de Engenharia Inform??tica.', '2021-11-01', TRUE
); -- Password: k53A!p4A
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jmpc@fe.up.qa.pt', 'jmpc', 
    '$2y$10$zz1PARycK.6xIknR4I6tMOt2WBd8FR.5YStNWO0n1doAAXkN5Pb7i', 
    'Teacher', 'Jo??o Manuel Paiva Cardoso', 'Received a 5-year Electronics Engineering degree from the University of Aveiro in 1993. He has been involved in the organization of various international conferences.', '2021-11-01', TRUE
); -- Password: @q6QVmrZ
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'aaguiar@fe.up.qa.pt', 'aaguiar', 
    '$2y$10$mpZimC.Nrap6QJgw5/Zd3ONWX1Q.h4AEje.fPk8j1xX2dvWLQFpl2', 
    'Teacher', 'Ademar Manuel Teixeira de Aguiar', 'Professor Associado na FEUP e investigador no INESC TEC, com mais de 30 anos de experiencia em desenvolvimento de software, especializou-se em arquitectura e design de software.', '2021-11-01', TRUE
); -- Password: vY&3Qu%U
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jpf@fe.up.qa.pt', 'jpf', 
    '$2y$10$JoTPzlthNAR8XQUj1TZBfOWIjcAVJTOJ2CZ19un3xlVXfbRb.o0Ji', 
    'Teacher', 'Jo??o Carlos Pascoal Faria', 'Doutoramento em Engenharia Electrot??cnica e de Computadores pela FEUP em 1999, onde ?? atualmente Professor Associado no Departamento de Engenharia Inform??tica e Diretor do Mestrado Integrado em Engenharia Inform??tica e Computa????o.', '2021-11-01', TRUE
); -- Password: NyXEB7#u
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'villate@fe.up.qa.pt', 'jev', 
    '$2y$10$F87pRSDz1luNCs04ZX5gLe5dtJZdwJrsU72JPf88jxga6OIwHnab.', 
    'Teacher', 'Jaime Enrique Villate Matiz', 'Licenciatura em F??sica, 1983, Universidade Nacional de Col??mbia, Bogot??. Licenciatura em Engenharia de Sistemas (Inform??tica), 1984, Universidade Distrital de Bogot??, Col??mbia. Master of  Arts em F??sica, 1987 e Ph. D. em F??sica, 1990...', '2021-11-01', TRUE
); -- Password: Q9&wdJkS
INSERT INTO "users" (email, username, password, type, name, about, registry_date, active) VALUES (
    'jmcruz@fe.up.qa.pt', 'mmc', 
    '$2y$10$jMgnjZFsJQgF7d.YY4Ks1OCBpty/LbyP//edm.rGbDzayKI15ejQ.', 
    'Teacher', 'Jos?? Manuel De Magalh??es Cruz', 'Docente na FEUP', '2021-11-01', TRUE
); -- Password: _Pb7nXv2

-----------------------------------------
-- uc
-----------------------------------------

INSERT INTO "uc" (name, code, description) VALUES ('Fundamentos de Seguran??a Inform??tica', 'FSI', 'Visa dotar os estudantes de uma vis??o abrangente dos aspetos de seguran??a inerentes ao desenvolvimento e opera????o de sistemas inform??ticos.');
INSERT INTO "uc" (name, code, description) VALUES ('Linguagens e Tecnologias Web', 'LTW', 'Desenvolve compet??ncias nas linguagens e tecnologias WEB, no contexto tecnol??gico atual, ou que foram determinantes no processo evolutivo da WEB.');
INSERT INTO "uc" (name, code, description) VALUES ('Laborat??rio de Bases de Dados e Aplica????es Web', 'LBAW', 'Oferece uma perspetiva pr??tica sobre duas ??reas centrais da engenharia inform??tica: bases de dados e linguagens e tecnologias web.');
INSERT INTO "uc" (name, code, description) VALUES ('Programa????o Funcional e em L??gica', 'PFL', 'Os paradigmas de Programa????o Funcional e de Programa????o em L??gica apresentam abordagens declarativas e baseadas em processos formais de racioc??nio ?? programa????o.');
INSERT INTO "uc" (name, code, description) VALUES ('Redes de Computadores', 'RC', 'Introduz os estudantes no dom??nio de conhecimento das redes de comunica????es: canais de comunica????o e controlo da liga????o de dados, modelos de erro e atraso...');
INSERT INTO "uc" (name, code, description) VALUES ('Compiladores', 'C', 'Fornecer os conceitos que permitam: compreender as fases de compila????o de linguagens, em especial das linguagens imperativas e orientada por objectos; especificar a sintaxe...');
INSERT INTO "uc" (name, code, description) VALUES ('Computa????o Paralela e Distribu??da', 'CPD', 'Introdu????o ?? computa????o paralela. Medidas de desempenho. M??quinas paralelas. Organiza????o de mem??ria e efeito da gest??o da mem??ria cache no desempenho do processador...');
INSERT INTO "uc" (name, code, description) VALUES ('Engenharia de Software', 'ES', 'Familiarizar-se com os m??todos de engenharia e gest??o necess??rios ao desenvolvimento de sistemas de software complexos e/ou em larga escala, de forma economicamente eficaz...');
INSERT INTO "uc" (name, code, description) VALUES ('Intelig??ncia Artificial', 'IA', 'Esta unidade curricular apresenta um conjunto de assuntos nucleares para a ??rea da Intelig??ncia Artificial e dos Sistemas Inteligentes.');
INSERT INTO "uc" (name, code, description) VALUES ('Projeto Integrador', 'PI', 'Esta unidade curricular pretende exp??r os estudantes a um projeto de Engenharia Inform??tica, de dom??nio e escala reais.');
INSERT INTO "uc" (name, code, description) VALUES ('Sistemas Operativos', 'SO', 'Os objetivos principais desta unidade curricular s??o fornecer os conhecimentos fundamentais sobre: O1- a estrutura e o funcionamento de um sistema operativo gen??rico;...');
INSERT INTO "uc" (name, code, description) VALUES ('F??sica II', 'F II', 'Atualmente o processamento, armazenamento e transmiss??o de informa????o s??o feitos usando fen??menos eletromagn??ticos. Consequentemente, a forma????o de base de um engenheiro inform??tico...');

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
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Como fazer um Wireframe?', 'N??o sei que aplica????o usar. Qual ?? a melhor e mais f??cil de usar?', 3, '2021-11-04 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Sitemap:pode existir liga????o componente-p??gina?', '?? correto ligar uma p??gina diretamente a uma componente de outra p??gina?', 3, '2021-11-03 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (11, 'Qual o nome do professor de LTW?', 'N??o consigo entrar no sigarra e preciso mesmo de saber o nome do professor...', 2, '2021-11-06 13:00:00', 'question');

INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (10, 'Uma Promisse encontra-se sempre no estado pendente.', 'Na fun????o update de comunica????o com o servidor o servidor n??o retorna nenhum valor, porqu???', 2, '2021-12-20 9:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Qundo se deve utilizar PUT ou POST?', 'N??o entendo a diferen??a entre PUT e POST, algu??m explica melhor que a documenta????o?', 2, '2021-11-06 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (13, 'Que comandos s??o utilzados para alterar as configura????es do switch?', 'Preciso de alterar as configrua????es de um switch par aa atividade laboratorial, mas a docuemnta????o ?? inexistente, algu??m sabe como?', 5, '2021-12-06 16:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Quais s??o as consequ??ncias de n??o usarmos mecanismos de mitiga????o?', 'Quais s??o as consequ??ncias de n??o usarmos mecanismos de mitiga????o?', 1, '2021-11-14 13:00:00', 'question');
INSERT INTO "intervention" (id_author, title, text, category, date, type) VALUES (12, 'Como fazer um string format exploit?', 'N??o entendo como se utiliza o %n nos exploits, principalmente quando o buffer tem limite de tamanho.', 1, '2021-11-26 13:00:00', 'question');


-- answer
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'O invision ?? o melhor embora n??o permita por textos por cima de caixas brancas. H?? tamb??m o figma, mas ?? bastante mais complexo.', 1, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'Faz no papel e digitaliza, n??o h?? nada melhor.', 1, '2021-11-05 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'Jos?? Paulo Leal...', 3, '2021-11-06 13:23:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Nome: Jos?? Paulo de Vilhena Geraldes Leal, Email: jpleal@fc.up.pt', 3, '2021-11-06 13:59:00', 'answer');

INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'H?? duas op????es: ou o servidor est?? mal ou tens que fazer mais alguma coisa al??m da fun????o update. Experimenta fazer correr em dois pcs com users diferentes para ver se o jogo j?? emparelha, aceitando a Promisse', 4, '2021-12-20 17:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Promise ?? um objeto usado para processamento ass??ncrono. Um Promise (de "promessa") representa um valor que pode estar dispon??vel agora, no futuro ou nunca.', 4, '2021-12-20 18:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Usas o POST quando pretendes que algo no servidor altere o seu estado e que fazendo novamente o retorno poderia n??o ser o mesmo, O PUT usas em caso contr??rio.', 5, '2021-11-09 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'Ningu??m sabe por isso vai experimentando e pode ser que d??.', 6, '2021-12-07 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Reflete nestes: enable, password:, configure terminal, vlan x0, end, show vlan id x0.', 6, '2021-12-08 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (11, 'Ficas sem clientes, pois n??o consegues dar garantias de confian??a no teu sistema...', 7, '2021-11-15 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (10, 'Primeiro tens de ter controlo sobre a string do printf e tem de ser algo do tipo: printf(input), para poderes considerar sequer uma vulnerabilidade...', 8, '2021-12-06 13:00:00', 'answer');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (13, 'Usas uma sequ??ncia de %x para ver o endere??o da stua string de input e depois %s ai, e tomas partido disso para acederes a endere??os de outros sitios.', 8, '2021-12-20 18:30:00', 'answer');


-- comment
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES (12, 'Outra ferramenta que podes usar ?? o draw.io', 9, '2021-11-05 14:00:00', 'comment');
INSERT INTO "intervention" (id_author, text, id_intervention, date, type) VALUES ( 6, 'O figma ?? o melhor!', 9, '2021-11-05 14:21:00', 'comment');

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

INSERT INTO "notification" (date, type, id_user, status) VALUES ('2021-11-10 15:00:00', 'account_status', 9, 'active');

-----------------------------------------
-- receive_not
-----------------------------------------

INSERT INTO "receive_not" (id_notification, id_user, read) VALUES (28, 9, FALSE);
