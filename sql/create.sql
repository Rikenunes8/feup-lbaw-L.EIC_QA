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
    email          TEXT NOT NULL CONSTRAINT email_uk UNIQUE ,
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
