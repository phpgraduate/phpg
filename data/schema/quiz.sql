CREATE TABLE quiz
(
    id        		SERIAL NOT NULL,
    title       	VARCHAR(100) NOT NULL,
    description 	TEXT NOT NULL,
    time      		INTEGER NOT NULL,
    num_questions   INTEGER NOT NULL,
    is_active       BOOLEAN DEFAULT NULL,
    is_opened	 	BOOLEAN DEFAULT NULL,
    PRIMARY KEY (id)
);

insert into quiz values(1, 'TestQuiz', 'Test', 10, 5, 'TRUE', 'FALSE');

CREATE TABLE questions
(
    id        	SERIAL NOT NULL,    
    text	 	TEXT NULL,
    type      	VARCHAR(50) DEFAULT NULL,
    file       	VARCHAR(255) DEFAULT NULL,
    num_answers INTEGER NOT NULL,
    PRIMARY KEY (id)  
);

insert into questions values(1, '1+1=?', NULL, NULL, 4 );

CREATE TABLE quiz_questions
(
	id        	SERIAL NOT NULL,
    question_id INTEGER NOT NULL,    
    quiz_id	 	INTEGER DEFAULT NULL,
    PRIMARY KEY (id),        
    CONSTRAINT question_id FOREIGN KEY (question_id)
    REFERENCES public.questions (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
    CONSTRAINT quiz_id FOREIGN KEY (quiz_id)
    REFERENCES public.quiz(id)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
    NOT DEFERRABLE  
);

insert into quiz_questions values(1, 1, 1);

CREATE TABLE answers
(
    id        	SERIAL NOT NULL,    
    question_id	INTEGER NOT NULL,
    title	 	VARCHAR(255) DEFAULT NULL,
    file      	VARCHAR(255) DEFAULT NULL,
    is_right    BOOLEAN DEFAULT NULL,    
    PRIMARY KEY (id),      
    CONSTRAINT question_id FOREIGN KEY (question_id)
    REFERENCES public.questions (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE
);

insert into answers values(1, 1, '2', NULL, 'TRUE'), (2, 1, '4', NULL, 'FALSE'), (3, 1, '3', NULL, 'FALSE'), (4, 1, '1', NULL, 'FALSE');

CREATE TABLE users_quiz
(
    id        			SERIAL NOT NULL,    
    user_id				INTEGER NOT NULL,
    quiz_id				INTEGER NOT NULL,
    questions	 		VARCHAR(255) NOT NULL,
    current      		INTEGER NOT NULL,
    num_right_answers   INTEGER DEFAULT NULL,
    started_at 			TIMESTAMP NULL,
    stopped_at 			TIMESTAMP NULL,
    is_active 			BOOLEAN DEFAULT NULL,
    is_closed 			BOOLEAN DEFAULT NULL,
    PRIMARY KEY (id),      
    CONSTRAINT quiz_id FOREIGN KEY (quiz_id)
    REFERENCES public.quiz (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
    CONSTRAINT user_id FOREIGN KEY (user_id)
    REFERENCES public.user (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE
);

insert into users_quiz values(1, 1, 1, '2', NULL, 'TRUE')

CREATE TABLE users_questions
(
    id        		SERIAL NOT NULL,    
    user_id			INTEGER NOT NULL,
    quiz_id			INTEGER NOT NULL,
    question_id		INTEGER NOT NULL,
    user_quiz_id	INTEGER NOT NULL,
    answer_id		INTEGER NOT NULL,
    answer_text	 	VARCHAR(200) DEFAULT NULL,
    is_right      	BOOLEAN DEFAULT NULL,
    is_closed    	BOOLEAN DEFAULT NULL,    
    PRIMARY KEY (id),      
    CONSTRAINT question_id FOREIGN KEY (question_id)
    REFERENCES public.questions (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
        CONSTRAINT quiz_id FOREIGN KEY (quiz_id)
    REFERENCES public.quiz (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
    CONSTRAINT user_id FOREIGN KEY (user_id)
    REFERENCES public.user (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
        CONSTRAINT answer_id FOREIGN KEY (answer_id)
    REFERENCES public.answers (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
        CONSTRAINT user_quiz_id FOREIGN KEY (user_quiz_id)
    REFERENCES public.users_quiz (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE
);

CREATE TABLE users_groups
(
	id        	SERIAL NOT NULL,
    group_id	INTEGER NOT NULL,    
    user_id		INTEGER NOT NULL,    
    PRIMARY KEY (id),      
    CONSTRAINT group_id FOREIGN KEY (group_id)
    REFERENCES public.group(id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE,
    CONSTRAINT user_id FOREIGN KEY (user_id)
    REFERENCES public.user (id)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT
    NOT DEFERRABLE
);