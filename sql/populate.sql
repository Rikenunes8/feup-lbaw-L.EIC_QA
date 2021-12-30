-----------------------------------------
-- TRANSACTIONS
-----------------------------------------

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

-----------------------------------------
-- user
-----------------------------------------
-- Password is always 1234. Generated using Hash::make('1234')

-- Admin
INSERT INTO "users" (email, username, password, type, score, blocked, registry_date) VALUES (
    'jfcunha@fe.up.pt', 'admin', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Admin', NULL, NULL, '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, score, blocked, registry_date) VALUES (
    'percurso.academico@fe.up.pt', 'secretaria', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Admin', NULL, NULL, '2021-11-01'
);

-- Teacher
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'mbb@fc.up.pt', 'mbb', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Manuel Bernardo Martins Barbosa', 'My research interests lie in Cryptography and Information Security and Formal Verification.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'jpleal@fc.up.pt', 'jpleal', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'José Paulo de Vilhena Geraldes Leal', 'Para além de professor, interesso-me por escrever livros pedagógicos.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'ssn@fe.up.pt', 'ssn', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Sérgio Sobral Nunes', 'I am an Assistant Professor at the Department of Informatics Engineering at the Faculty of Engineering of the University of Porto (FEUP), and a Senior Researcher at the Centre for Information Systems and Computer Graphics at INESC TEC.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'tbs@fe.up.pt', 'tbs', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Tiago Boldt Pereira de Sousa', 'Conclui o Mestrado em Mestrado Integrado em Engenharia Informática e Computação em 2011 pela Universidade do Porto Faculdade de Engenharia. Publiquei 5 artigos em revistas especializadas.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'amflorid@fc.up.pt', 'amflorid', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'António Mário da Silva Marcos Florido', 'Sou investigador e membro da direção do Laboratório de Inteligência Artificial e Ciência de Computadores (LIACC) da FCUP.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'mricardo@fe.up.pt', 'mricardo', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Manuel Alberto Pereira Ricardo', 'Licenciado, Mestre e Doutor (2000) em Engenharia Eletrotécnica e de Computadores, ramo de Telecomunicações, pela Faculdade de Engenharia da Universidade do Porto (FEUP).', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'pabranda@fc.up.pt', 'pabranda', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Pedro Miguel Alves Brandão', 'Fiz o meu doutoramento no Computer Laboratory da Univ. de Cambridge sobre o tema de Body Sensor Networks. Obtive uma bolsa da Fundação para a Ciência e Tecnologia para suporte ao doutoramento.', '2021-11-01'
);

-- Student
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (
    'up201805455@fc.up.pt', 'up201805455', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Alexandre Afonso', '2000-07-23 11:00:00', 2018, '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (
    'up201906852@fe.up.pt', 'up201906852', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Henrique Nunes', '2001-02-08 13:00:00', 2019, '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (
    'up201905427@fe.up.pt', 'up201905427', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Patrícia Oliveira', '2001-03-19 17:00:00', 2019, '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date) VALUES (
    'up201805327@fc.up.pt', 'up201805327', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Tiago Antunes', '2000-06-10 11:00:00', 2018, '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (
    'up201905046@fe.up.pt', 'up201905046', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Margarida Ribeiro', '2001-06-10 11:00:00', 2019, '2021-11-01', TRUE, 'Abuso de permissões'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (
    'up201476549@fc.up.pt', 'up201476549', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Francisco Mendes', '1996-11-07 11:00:00', 2014, '2021-11-01', TRUE, 'Conteúdos impróprios'
);
INSERT INTO "users" (email, username, password, type, name, birthdate, entry_year, registry_date, blocked, block_reason) VALUES (
    'up201823452@fe.up.pt', 'up201823452', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Student', 'Ana Martins', '2000-08-24 11:00:00', 2018, '2021-11-01', TRUE, 'Conta foi hackeada'
);

-- More Teachers
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'lpreis@fe.up.pt', 'lpreis', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Luís Paulo Gonçalves dos Reis', 'Licenciado (1993), Mestre (1995) e Doutor (2003) em Engenharia Eletrotécnica e de Computadores (especializações em Informática e Sistemas, Informática Industrial, Inteligência Artificial/Robótica) pela Universidade do Porto.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'pfs@fe.up.pt', 'pfs', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Pedro Alexandre Guimarães Lobo Ferreira Souto', 'Professor Auxiliar, Departamento de Engenharia Informática.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'jmpc@fe.up.pt', 'jmpc', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'João Manuel Paiva Cardoso', 'Received a 5-year Electronics Engineering degree from the University of Aveiro in 1993. He has been involved in the organization of various international conferences.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'aaguiar@fe.up.pt', 'aaguiar', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Ademar Manuel Teixeira de Aguiar', 'Professor Associado na FEUP e investigador no INESC TEC, com mais de 30 anos de experiencia em desenvolvimento de software, especializou-se em arquitectura e design de software.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'jpf@fe.up.pt', 'jpf', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'João Carlos Pascoal Faria', 'Doutoramento em Engenharia Electrotécnica e de Computadores pela FEUP em 1999, onde é atualmente Professor Associado no Departamento de Engenharia Informática e Diretor do Mestrado Integrado em Engenharia Informática e Computação.', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'villate@fe.up.pt', 'jev', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'Jaime Enrique Villate Matiz', 'Licenciatura em Física, 1983, Universidade Nacional de Colômbia, Bogotá. Licenciatura em Engenharia de Sistemas (Informática), 1984, Universidade Distrital de Bogotá, Colômbia. Master of  Arts em Física, 1987 e Ph. D. em Física, 1990...', '2021-11-01'
);
INSERT INTO "users" (email, username, password, type, name, about, registry_date) VALUES (
    'jmcruz@fe.up.pt', 'mmc', 
    '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 
    'Teacher', 'José Manuel De Magalhães Cruz', 'Docente na FEUP', '2021-11-01'
);

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
