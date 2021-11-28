-----------------------------------------
-- utilizador
-----------------------------------------

-- Administrador
INSERT INTO utilizador (id, mail, username, password, tipo, pontuacao, bloqueado, data_registo) VALUES (0, 'jfcunha@fe.up.pt', 'admin', 'admin', 'Administrador', NULL, NULL, '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, pontuacao, bloqueado, data_registo) VALUES (1, 'percurso.academico@fe.up.pt', 'secretaria', 'p-academico', 'Administrador', NULL, NULL, '2021-11-01');

-- Docente
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (2, 'mbb@fc.up.pt', 'mbb', 'mbb', 'Docente', 'Manuel Bernardo Martins Barbosa', 'My research interests lie in Cryptography and Information Security and Formal Verification.', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (3, 'jpleal@fc.up.pt', 'jpleal', 'jpleal', 'Docente', 'José Paulo de Vilhena Geraldes Leal', 'Para além de professor, interesso-me por escrever livros pedagógicos.', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (4, 'ssn@fe.up.pt', 'ssn', 'ssn', 'Docente', 'Sérgio Sobral Nunes', 'I am an Assistant Professor at the Department of Informatics Engineering at the Faculty of Engineering of the University of Porto (FEUP), and a Senior Researcher at the Centre for Information Systems and Computer Graphics at INESC TEC.', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (5, 'tbs@fe.up.pt', 'tbs', 'tbs', 'Docente', 'Tiago Boldt Pereira de Sousa', 'Conclui o Mestrado em Mestrado Integrado em Engenharia Informática e Computação em 2011 pela Universidade do Porto Faculdade de Engenharia. Publiquei 5 artigos em revistas especializadas.', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (6, 'amflorid@fc.up.pt', 'amflorid', 'amflorid', 'Docente', 'António Mário da Silva Marcos Florido', 'Sou investigador e membro da direção do Laboratório de Inteligência Artificial e Ciência de Computadores (LIACC) da FCUP.', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (7, 'mricardo@fe.up.pt', 'mricardo', 'mricardo', 'Docente', 'Manuel Alberto Pereira Ricardo', 'Licenciado, Mestre e Doutor (2000) em Engenharia Eletrotécnica e de Computadores, ramo de Telecomunicações, pela Faculdade de Engenharia da Universidade do Porto (FEUP).', '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, sobre, data_registo) VALUES (8, 'pabranda@fc.up.pt', 'pabranda', 'pabranda', 'Docente', 'Pedro Miguel Alves Brandão', 'Fiz o meu doutoramento no Computer Laboratory da Univ. de Cambridge sobre o tema de Body Sensor Networks. Obtive uma bolsa da Fundação para a Ciência e Tecnologia para suporte ao doutoramento.', '2021-11-01');

-- Aluno
INSERT INTO utilizador (id, mail, username, password, tipo, nome, data_nascimento, ano_ingresso, data_registo) VALUES ( 9, 'up201805455@fc.up.pt', 'up201805455', 'up201805455', 'Aluno', 'Alexandre Afonso', '2000-07-23 11:00:00', 2018, '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, data_nascimento, ano_ingresso, data_registo) VALUES (10, 'up201906852@fe.up.pt', 'up201906852', 'up201906852', 'Aluno', 'Henrique Nunes', '2001-02-08 13:00:00', 2019, '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, data_nascimento, ano_ingresso, data_registo) VALUES (11, 'up201905427@fe.up.pt', 'up201905427', 'up201905427', 'Aluno', 'Patrícia Oliveira', '2001-03-19 17:00:00', 2019, '2021-11-01');
INSERT INTO utilizador (id, mail, username, password, tipo, nome, data_nascimento, ano_ingresso, data_registo) VALUES (12, 'up201805327@fc.up.pt', 'up201805327', 'up201805327', 'Aluno', 'Tiago Antunes', '2000-06-10 11:00:00', 2018, '2021-11-01');

-----------------------------------------
-- uc
-----------------------------------------

INSERT INTO uc (id, nome, sigla, descricao) VALUES (0, 'Fundamentos de Segurança Informática', 'FSI', 'Visa dotar os estudantes de uma visão abrangente dos aspetos de segurança inerentes ao desenvolvimento e operação de sistemas informáticos.');
INSERT INTO uc (id, nome, sigla, descricao) VALUES (1, 'Linguagens e Tecnologias Web', 'LTW', 'Desenvolve competências nas linguagens e tecnologias WEB, no contexto tecnológico atual, ou que foram determinantes no processo evolutivo da WEB.');
INSERT INTO uc (id, nome, sigla, descricao) VALUES (2, 'Laboratório de Bases de Dados e Aplicações Web', 'LBAW', 'Oferece uma perspetiva prática sobre duas áreas centrais da engenharia informática: bases de dados e linguagens e tecnologias web.');
INSERT INTO uc (id, nome, sigla, descricao) VALUES (3, 'Programação Funcional e em Lógica', 'PFL', 'Os paradigmas de Programação Funcional e de Programação em Lógica apresentam abordagens declarativas e baseadas em processos formais de raciocínio à programação.');
INSERT INTO uc (id, nome, sigla, descricao) VALUES (4, 'Redes de Computadores', 'RC', 'Introduz os estudantes no domínio de conhecimento das redes de comunicações: canais de comunicação e controlo da ligação de dados, modelos de erro e atraso...');

-----------------------------------------
-- docente_uc
-----------------------------------------

INSERT INTO docente_uc (id_docente, id_uc) VALUES (2, 0);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (3, 1);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (4, 2);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (5, 2);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (6, 3);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (7, 8);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (8, 8);
INSERT INTO docente_uc (id_docente, id_uc) VALUES (8, 2);

-----------------------------------------
-- segue_uc
-----------------------------------------

INSERT INTO segue_uc (id_aluno, id_uc) VALUES (9, 2);
INSERT INTO segue_uc (id_aluno, id_uc) VALUES (12, 2);
INSERT INTO segue_uc (id_aluno, id_uc) VALUES (10, 0);
INSERT INTO segue_uc (id_aluno, id_uc) VALUES (10, 3);
INSERT INTO segue_uc (id_aluno, id_uc) VALUES (11, 1);

-----------------------------------------
-- intervencao
-----------------------------------------

-- questao
INSERT INTO intervencao (id, id_autor, titulo, texto, categoria, data, tipo, pontuacao) VALUES (0, 10, 'Como fazer um Wireframe?', 'Não sei que aplicação usar. Qual é a melhor e mais fácil de usar?', 2, '2021-11-04 13:00:00', 'questao', 78);
INSERT INTO intervencao (id, id_autor, titulo, texto, categoria, data, tipo, pontuacao) VALUES (1, 10, 'Sitemap:pode existir ligação componente-página?', 'É correto ligar uma página diretamente a uma componente de outra página?', 2, '2021-11-03 13:00:00', 'questao', 1);
INSERT INTO intervencao (id, id_autor, titulo, texto, categoria, data, tipo, pontuacao) VALUES (2, 10, 'Qual o nome do profeddor de LTW?', 'Não consigo entrar no sigarra e preciso mesmo de saber o nome do professor...', 1, '2021-11-06 13:00:00', 'questao', 0);

-- resposta
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo, pontuacao) VALUES (3, 9, 'O invision é o melhor embora não permita por textos por cima de caixas brancas. Há também o figma, mas é bastante mais complexo.', 0, '2021-11-05 13:00:00', 'resposta', 110);
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo, pontuacao) VALUES (4, 12, 'Faz no papel e digitaliza, não há nada melhor.', 0, '2021-11-05 13:00:00', 'resposta', 3);
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo, pontuacao) VALUES (5, 12, 'José Paulo Leal...', 2, '2021-11-06 13:23:00', 'resposta', 2);
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo, pontuacao) VALUES (6, 9, 'Nome: José Paulo de Vilhena Geraldes Leal, Email: jpleal@fc.up.pt', 2, '2021-11-06 13:59:00', 'resposta', 10);

-- comentario
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo) VALUES (7, 11, 'Outra ferramenta que podes usar é o draw.io', 3, '2021-11-05 14:00:00', 'comentario');
INSERT INTO intervencao (id, id_autor, texto, id_intervencao, data, tipo) VALUES (8,  5, 'O figma é o melhor!', 3, '2021-11-05 14:21:00', 'comentario');

-----------------------------------------
-- votacao
-----------------------------------------

INSERT INTO votacao (id_utilizador, id_intervencao, voto) VALUES (11, 3, TRUE);
INSERT INTO votacao (id_utilizador, id_intervencao, voto) VALUES ( 5, 3, TRUE);
INSERT INTO votacao (id_utilizador, id_intervencao, voto) VALUES ( 9, 2, FALSE);
INSERT INTO votacao (id_utilizador, id_intervencao, voto) VALUES (11, 2, FALSE);
INSERT INTO votacao (id_utilizador, id_intervencao, voto) VALUES (12, 2, FALSE);

-----------------------------------------
-- validacao
-----------------------------------------

INSERT INTO validacao (id_resposta, id_docente, valida) VALUES (3, 5, TRUE); 
INSERT INTO validacao (id_resposta, id_docente, valida) VALUES (5, 3, TRUE); 
INSERT INTO validacao (id_resposta, id_docente, valida) VALUES (6, 3, TRUE); 

-----------------------------------------
-- razao_bloqueio
-----------------------------------------

INSERT INTO razao_bloqueio (id, razao) VALUES (0, 'Conteúdo inapropriado');
INSERT INTO razao_bloqueio (id, razao) VALUES (1, 'Conta foi roubada');
INSERT INTO razao_bloqueio (id, razao) VALUES (2, 'Uso irresponsável do sistema');
INSERT INTO razao_bloqueio (id, razao) VALUES (3, 'Conteúdo ofensivo');
INSERT INTO razao_bloqueio (id, razao) VALUES (4, 'Outro');

-----------------------------------------
-- notificacao
-----------------------------------------

INSERT INTO notificacao (id, data, tipo, id_razao, estado) VALUES (0, '2021-11-10 15:00:00', 'estado_conta', 4, 'ativacao');

-----------------------------------------
-- recebe_not
-----------------------------------------

INSERT INTO recebe_not (id_notificacao, id_utilizador, lida) VALUES (0, 8, FALSE);
