CREATE TABLE public.user (
  id SERIAL NOT NULL,
  username varchar(32) NOT NULL,
  password varchar(64) NOT NULL,  
  twitter varchar(32) DEFAULT NULL,
  is_admin integer DEFAULT 0,
  PRIMARY KEY (id)  
);

INSERT INTO public.user VALUES (1,'siteadmin','0192023a7bbd73250516f069df18b500',NULL,1 );
INSERT INTO public.user VALUES (2,'member','0192023a7bbd73250516f069df18b500');