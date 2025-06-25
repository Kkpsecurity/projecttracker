--
-- PostgreSQL database dump
--

-- Dumped from database version 9.6.22
-- Dumped by pg_dump version 9.6.22

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP INDEX public.project_name_idx;
DROP INDEX public.password_resets_email_index;
DROP INDEX public.client_name_idx;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
ALTER TABLE ONLY public.users DROP CONSTRAINT users_email_unique;
ALTER TABLE ONLY public.migrations DROP CONSTRAINT migrations_pkey;
ALTER TABLE ONLY public.failed_jobs DROP CONSTRAINT failed_jobs_pkey;
ALTER TABLE ONLY public.clients DROP CONSTRAINT clients_pkey;
ALTER TABLE public.users ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.migrations ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.failed_jobs ALTER COLUMN id DROP DEFAULT;
ALTER TABLE public.clients ALTER COLUMN id DROP DEFAULT;
DROP SEQUENCE public.users_id_seq;
DROP TABLE public.users;
DROP TABLE public.password_resets;
DROP SEQUENCE public.migrations_id_seq;
DROP TABLE public.migrations;
DROP SEQUENCE public.failed_jobs_id_seq;
DROP TABLE public.failed_jobs;
DROP SEQUENCE public.clients_id_seq;
DROP TABLE public.clients;
DROP EXTENSION plpgsql;
DROP SCHEMA public;
--
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: projecttracker
--

CREATE TABLE public.clients (
    id bigint NOT NULL,
    client_name character varying(255) NOT NULL,
    project_name character varying(255) NOT NULL,
    poc text,
    status text,
    quick_status text,
    description text,
    corporate_name text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    file1 character varying(255),
    file2 character varying(255),
    file3 character varying(255),
    project_services_total double precision,
    project_expenses_total double precision,
    final_services_total double precision,
    final_billing_total double precision
);


ALTER TABLE public.clients OWNER TO projecttracker;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: projecttracker
--

CREATE SEQUENCE public.clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO projecttracker;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: projecttracker
--

ALTER SEQUENCE public.clients_id_seq OWNED BY public.clients.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: projecttracker
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO projecttracker;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: projecttracker
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO projecttracker;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: projecttracker
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: projecttracker
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO projecttracker;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: projecttracker
--

CREATE SEQUENCE public.migrations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO projecttracker;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: projecttracker
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: projecttracker
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_resets OWNER TO projecttracker;

--
-- Name: users; Type: TABLE; Schema: public; Owner: projecttracker
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO projecttracker;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: projecttracker
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO projecttracker;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: projecttracker
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: clients id; Type: DEFAULT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.clients ALTER COLUMN id SET DEFAULT nextval('public.clients_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: projecttracker
--

COPY public.clients (id, client_name, project_name, poc, status, quick_status, description, corporate_name, created_at, updated_at, file1, file2, file3, project_services_total, project_expenses_total, final_services_total, final_billing_total) FROM stdin;
6	Kohl's	Active Shooter Assessment of Kohl's Facilities	N/A - All is going right now through Kohl's procurement hub.	Received word they did not want to proceed with our participation further in the RFP.	Closed	Active shooter assessment of four Kohl's facilities including corporate headquarters, another office complex, one distribution center, and one retail center.	CIS	2021-06-02 21:05:37	2021-06-16 12:24:53	\N	\N	\N	\N	\N	\N	\N
10	Precept Management	Private ATO Course (Cyprus)	Nick\r\nPrecept Management Consultancy	Nick has agreed to pricing and dates (6th-10th September): $11,500 plus travel expenses.\r\n\r\nJust awaiting confirmation from his Cypriot government client. I marked it as Active since pricing and dates are set, but it is still pending final confirmation from the Govt.	Closed	Private ATO program for Cypriot security professionals. Location will most likely be Nicosia.	S2	2021-06-03 13:04:16	2021-07-20 18:00:18	\N	\N	\N	\N	\N	\N	\N
14	Bombardier Aerospace	Active Shooter Assessment for Red Oak, TX Plant	Gloria\r\n514-297-4548	Left a voicemail seeking an update on 23 June.\r\n---------\r\nI spoke with the client on 6/15/2021. Client is eager to have an assessment completed of their Red Oak, TX plant. When speaking, I gave her a conservative budget estimate of $9,000. I am awaiting her approval after speaking with her management.	Closed	\N	CIS	2021-06-16 12:31:21	2022-11-25 16:24:06	\N	\N	\N	\N	\N	\N	\N
1	eu-LISA	ATO Program (Strasbourg)	CONTRACT POC: Anna Schmidt\r\nEMAIL: aschmidt@infeurope.lu\r\n\r\nEU-LISA POC: Jean-Pierre Zinzen\r\nEMAIL: Jean-Pierre.ZINZEN@EULISA.EUROPA.EU	On 31 May, dates were confirmed for private ATO program. Waiting for purchase order or contract to confirm everything.	Closed	Private ATO course for eu-LISA security personnel in Strasbourg, France. Dates are now set for 11-15 October 2021.\r\n\r\nInclusive price is EUR 18.586,00	S2	2021-06-02 20:28:18	2022-11-25 16:24:35	eu-LISA_Proposal_SecurityTraining_06212021 (UPDATED).pdf	\N	\N	\N	\N	\N	\N
4	Invictus	Physical Security Plan for Cannabis Facility in MA	Pat Miller	Sent Pat our proposal on 6/16/2021. Total estimate is $24,445.00.	Closed	Develop a physical security plan and design for a company preparing for a MA cannabis cultivation license. May also include supervision of implementation and getting the security program underway after the license is approved.	CIS	2021-06-02 20:50:37	2022-11-25 16:24:54	pjiS0jKC0TLGHtGiZggTTWfP1LfZMwLp3OfUvUgW.doc	Proposal to Fried Law Group - JUN2021.doc	\N	\N	\N	\N	\N
15	Barton County	Barton County School Threat Assessment Program	Sue Cooper\r\nscooper@bartoncounty.org\r\nTel. (620) 793-1800	Proposal was sent on 23 June. I expect we will have a phone conference within the next few weeks to work out any details or changes needed for her grant applicaiton.	Closed	Barton County is applying for the BJA STOP school violence grant program and needs a quote to develop a school threat assessment program for three rural school districts, located in Barton County, Kansas.	CIS	2021-06-16 12:38:41	2022-11-25 16:25:07	Proposal to Barton County - Threat Assessment Program - 06212021.pdf	\N	\N	\N	\N	\N	\N
9	Swedish Civil Contingencies Agency (MSB)	Custom ATO Course (Stockholm)	Petter Säterhed\r\nSwedish National Police, The Swedish Civil Contingencies Agency (MSB)\r\npetter.saterhed@msb.se	Communicated with Petter during the week of 29 May about doing a private ATO program for his group in Stockholm. He is examining budget on his end. I need to follow up by 07 June of I don't hear sooner.	Closed	Custom ATO program in Stockholm for Swedish police assigned to protecting public locations. Will likely charge $13,000 plus travel expenses and try to schedule adjacent to another trip to minimize cost and travel time.	S2	2021-06-03 13:00:47	2022-11-25 16:25:24	\N	\N	\N	\N	\N	\N	\N
2	European Parliament	Private ATO Coursea (Brussels)	Mariana KRAJCOVA \r\nAdministrative manager \r\nEuropean Parliament \r\nDirectorate-General for Security and Safety \r\nDirectorate for Strategy and Resources \r\nTraining Unit\r\nBRU - SPINELLI 07D84 - Tel. +32 228 31472 \r\nCell phone: +32 470 89 34 72\r\nmariana.krajcova@europarl.europa.eu \r\n\r\nwww.europarl.europa.eu	Completing second course the week of 11/27/2022\r\nThird course scheduled for 6 to 10 February 2023	Active	Private ATO course for European Parliament security personnel.\r\n\r\nContract amount: EUR  17.900,00 per session	S2	2021-06-02 20:37:57	2022-11-27 08:35:20	2019-002 FWK CT signed.pdf	Purchase order_2022_119.pdf	Purchase order_2022_152.pdf	\N	\N	\N	\N
3	Engineering Matrix, Inc	NFPA 72 Risk Assessment for Pinellas School	Greg Bowen\r\nEMAIL: gregb@engmtx.com	Sent him an email for an update on 23 June.\r\n----------------\r\nSent contract on 01 June.	Completed	Risk assessment for a new Pinellas school in the design process to meet new compliance requirements of NFPA 72.	CIS	2021-06-02 20:43:09	2022-12-16 17:56:27	CONSULTING AGREEMENT - Engineering Matrix Inc - 06012021.doc	\N	\N	0	0	0	0
5	HAI Group	LE Guidelines Review	JB Smith\r\nHAI Group\r\n189 Commerce Court, Cheshire CT 06410\r\nDirect 203-272-8220, ext. 351 | Toll Free 800-873-0242 | Cell 757-812-8291\r\njsmith@housingcenter.com	Project started. Terri & KC are working on first sample of report to send to HAI.	Completed	Expert review of HAI Group Guidelines. See proposal & contract for details.	CIS	2021-06-02 21:02:34	2022-12-16 17:56:37	Proposal to HAI Group  - Rev 02192021.pdf	\N	\N	0	0	0	0
7	Waterton	Environmental Risk Assessment for The Amelia (MA)	Nastassja Heintz-Janis\r\nNastassja.Heintz-Janis@waterton.com\r\n\r\nWilliam Aguiar\r\nWilliam.Aguiar@waterton.com	Completed.	Completed	Environmental Risk Assessment for The Amelia (MA).\r\n\r\nInclusive Price: $6,965.00	CIS	2021-06-02 21:10:08	2022-12-16 17:57:08	\N	\N	\N	0	0	0	0
11	The Greater Dayton School	The Greater Dayton School	A.J. Stich \r\nThe Greater Dayton School – Founding Principal \r\n10510 N Springboro Pike, Miamisburg OH 45342 \r\n(937) 434-3095, extension 3351 \r\nastich@greaterdayton.org\r\n\r\ngreaterdayton.org	Proposal sent on 23 June. They will advise is we advance to another interview.\r\n\r\nA.J. googled security consulting and we popped up.	Closed	Below is a brief outline of the work that will need done:\r\n•  Security schematic design - Working with architects to review security systems integrated into the architectural designs for the school.\r\n•  School Security Technology - Help us secure innovative technology solutions to enhance school security.  Coordinate with security technology vendors to properly install/manage systems.\r\n•  Master planning - Design a school security standard operating procedures master plan and manual, teacher/staff security planning training, advise us on security staffing.\r\nBelow are some details about the school:\r\n•  Building - 5 levels, 100,000 sq. ft. (new construction)\r\n•  Staff - 29 to start, building up to 60\r\n•  Students - 120 to start, building up to 400	CIS	2021-06-04 03:33:56	2022-11-25 16:23:32	Proposal to The Greater Dayton School - 06172021.pdf	\N	\N	\N	\N	\N	\N
19	Highway Transport	Security Assessment Project	Rick Lusby\r\nVice President of Safety and Fleet Services\r\nDirect \t(865) 474-8010\r\nMobile \t(865) 740-8046\r\nRLusby@highwaytransport.com	All physical assessments are complete - Need to finish preparing oral ROF\r\nOral ROF delivery scheduled for 13 December, 15:30 by Zoom	Active	Assessment of the Highway Transport Corporate Office and four other specified service center locations: Knoxville Service Center, Baton Rouge Service Center, Lake Charles Service Center, & Houston Service Center\r\n\r\nFinal billing upon completion: $28,940	CIS	2022-11-27 07:31:51	2022-11-27 07:32:32	Signed-Agreement.pdf	Proposal to Highway Transport - Revised 08162022.pdf	\N	\N	\N	\N	\N
20	Hall & Evans, LLC	Castillo v STEM (Expert Witness)	David M. Jones | Member\r\njonesd@hallevans.com\r\nTel: 303-628-3312\r\njonesd@hallevans.com	Report delivered on 11/24/2022\r\nWaiting for dates for deposition - Will need to assist in preparing for opposition expert deposition in December\r\nTrial is expected in February 2023\r\nWork completed to date: est. $65,000 - Presently owes us $40,000 in new retainers	Active	\N	CIS	2022-11-27 07:36:49	2022-11-27 07:36:49	\N	\N	\N	\N	\N	\N	\N
21	Saint Philips Episcopal Church & School	Church and School Security Assessment	Edward Diaz\r\nChief Operations Officer\r\nSaint Philip’s Episcopal Church and School\r\n1121 Andalusia Avenue\r\nCoral Gables, Florida 33134\r\nPhone (305) 444-6366\r\nediaz@saintphilips.net	Assessment scheduled 11-12 December - Hector & Craig participating\r\nNeed to write report immediately afterward	Active	Assessment of church and school with written report of findings.  Inclusive billing upon delivery of the report: $16,290	CIS	2022-11-27 07:45:08	2022-11-27 07:46:16	24990521_2_(MIADOCS)_St.PhilipsEpiscopal-CIS-Consulting-Agreement.pdf	Proposal to Saint Philips Episcopal Church and School - 08122022.pdf	\N	\N	\N	\N	\N
17	West Coast University	WCU Assessment Project	Rob Koran\r\nrkoran@westcoastuniversity.edu\r\n949-870-6501\r\n\r\nDavid Tran\r\nCell: 479-739-2662\r\nDaTran@westcoastuniversity.edu	Fort Richardson assessment is underway - Need to prepare oral ROF\r\nHector has scheduled three assessments in CA for the last week of November\r\nNeed to submit a schedule for the remainder of assessments in January-March	Active	Physical assessment of security and safety conditions at the following 14 WCU facilities and parking lots. See contract and proposal.\r\n\r\nBilled hourly and invoiced biweekly. Rates: $225 per hour for services and $162 per hour for travel	CIS	2022-11-27 07:18:26	2022-11-27 07:48:35	CIS Consulting Agreement for WCU - Campus Assessments-10192022.docx.pdf	Proposal to West Coast University - 07102022.pdf	\N	\N	\N	\N	\N
23	Florida Department of Health	Active Shooter Response Train-the-Trainer Program	Mary Register\r\nFlorida Department of Health\r\nDivision of Emergency Preparedness and Community Support\r\nBureau of Preparedness and Response\r\nTraining and Exercise Specialist\r\n4052 Bald Cypress Way Bin A-23\r\nTallahassee, FL 32399\r\nDesk:  850-245-4894 \r\nMobile:  850-445-9265\r\nMary.Register@FLHealth.gov	Proposal submitted on 11 November 2022	Proposal Sent	Develop & Present trainer-the-trainer (T-o-T) program focusing on active shooter response for 50 designated Department employees over two days. Training will be conducted on-site at an FDOH facility in Orlando. See proposal for details.\r\n\r\nIf both T-o-T sessions are scheduled concurrently as two one-day courses, the total price of services is $24,150.	CIS	2022-11-27 07:58:44	2022-11-27 07:59:11	Proposal to Florida Department of Health - 11102022.pdf	\N	\N	\N	\N	\N	\N
24	Oak Street Health	Security Assessments	Pryce Williams\r\npryce.williams@oakstreethealth.com	Hector & Craig discussed the project on Zoom with 0n 14 November. They are refining a short list of facilities for assessment before we submit a proposal.	New Lead	\N	CIS	2022-11-27 08:34:28	2022-11-27 08:34:28	\N	\N	\N	\N	\N	\N	\N
25	FLETC	Anti-Terrorism Officer Program	David A. Saunders\r\nSenior Instructor\r\nCounterterrorism Division\r\nOff:912-261-3672\r\nCell:912-944-7908\r\ndavid.a.saunders@fletc.dhs.gov	Discussed conducting a private ATO program at FLETC on 10/3/2022. Last message to Saunders:\r\n-------------------\r\nRegarding costs, for private groups with a hosted venue site, pricing is based on $12,000 per course (5-days/40-hours) plus expenses. If it was International, there would be an added fee for travel time but not relevant with a short trip to GA. I can accommodate up to 25 students in an ATO course. \r\n\r\nSo if it were contracted, we’d estimate the expenses and provide an inclusive rate (probably somewhere around $15,000 including expenses for GA). If the arrangement was to price for individual tuition, we would probably suggest a pricing model at $1,500 per student and a 10 student minimum. If the host is confident they can deliver a larger audience, that price can be lowered. In the end, what matters is that we arrive at a minimum of $15,000 gross for the course.\r\n\r\nFor organizations that want to host multiple sessions, we adjust that pricing according to scale. For example, Amazon recently requested a series of six sessions and we adjusted the base price to $11,000 per week (excluding expenses).	New Lead	\N	S2	2022-11-27 08:38:31	2022-11-27 08:38:31	\N	\N	\N	\N	\N	\N	\N
28	Tampa Hillsborough County Expressway Authority	Emergency Response Planning & Training	Gary Holland\r\nToll Systems Manager\r\nTampa Hillsborough County Expressway Authority\r\n1104 E. Twiggs Street, Suite 300\r\nTampa, Florida 33602\r\n813.610.2423\r\ngary.holland@tampa-xway.com	Need to schedule date for assessment in December.	Active	Security and emergency readiness audit of the THEA office, physical security\r\nanalysis, development of a comprehensive all-hazard Emergency Response Plan (EMP) specific\r\nto THEA’s facilities, and training Emergency Response Team members and THEA employees in\r\ncritical emergency response procedures.\r\n\r\nBilled in Four Phases. Total Price: $19,825.	CIS	2022-11-27 09:02:34	2022-11-27 09:04:20	CIS Original Agreement Executed.pdf	Proposal to Tampa Hillsborough County Expressway Authority - MAR2022.pdf	\N	\N	\N	\N	\N
29	Marysville Joint Unified School District	School Security Assessment & Program Improvement	Bryan Williams\r\nbwilliams@mjusd.k12.ca.us	Proposal sent on 11/5/2022	Proposal Sent	Assessment of security and safety conditions at twenty-three MJUSD schools. See proposal for details.\r\nHourly billing: $225 | Travel: $175\r\n\r\nTotal Estimated cost: $122,000	CIS	2022-11-27 09:32:07	2022-11-27 09:35:28	Marysville Joint Unified School District Security Assessment Proposal - 2022.pdf	\N	\N	\N	\N	\N	\N
30	Third Baptist Church of San Francisco	Church Security Assessment	Jamie Muntner\r\nmailto:jamie@thirdbaptist.org	Responded on 06 December. Haven't heard back yet.	New Lead	Third Baptist Church of San Francisco would like a comprehensive security consultation for our property and operations.	CIS	2022-12-09 12:08:15	2022-12-09 12:08:15	\N	\N	\N	\N	\N	\N	\N
22	Savannah Christian Preparatory School	School Security Assessment	Jeff Plunk\r\nHead of School\r\nTel. 912-721-1763\r\njplunk@savcps.com	Scheduled for January 30 and 31. Sent info request.	Active	School security assessment with oral report of findings. Inclusive Price: $12,550	CIS	2022-11-27 07:53:40	2022-12-09 12:10:55	Proposal to SCPS  - 10262022.pdf	Agreement-CIS-SCPS-11302022.pdf	\N	\N	\N	\N	\N
27	North Cobb Christian School	SPO Course & Active Shooter Tactical Training	\N	Need to prepare a proposal for Todd. Agreed on price of $11,000.	New Lead	NCCS wants to conduct an SPO in the Summer of 2023. Todd was advised the price would be approximately $12,000 altogether including expenses. Course will be taught by Craig an Shannon (tactical).	CIS	2022-11-27 08:55:19	2022-12-09 12:15:35	\N	\N	\N	\N	\N	\N	\N
31	West Coast University	REFERRAL - Window Film for WCU	Scott McCutcheon\r\nEmerald Coast Glass Protection\r\n p: 850-832-5859 \r\ne: scott@glassprotectionconsulting.com	WCU wants Scott McCutcheon, Emerald Coast Glass Protection, to do an assessment of Richardson and start working on a solution for implementing our recommendations. I'll need to work with Scott on a referral fee for us once he gets the project underway.	New Lead	\N	CIS	2022-12-09 13:10:54	2022-12-09 13:10:54	\N	\N	\N	\N	\N	\N	\N
32	Greystar	Property Assessments	Sal Ariganello\r\nSAL.ARIGANELLO@GREYSTAR.COM	Inquiry received on 09 December: "I am on the investment team at Greystar. We are looking to set up a meeting to discuss possible crime consultations for some of our properties on the East Coast."\r\n\r\nSent reply seeking a day and time to speak.	New Lead	\N	CIS	2022-12-09 18:46:38	2022-12-09 18:46:38	\N	\N	\N	\N	\N	\N	\N
26	Prometheus Real Estate Group	Property Assessments for The Dean and The Hadley Properties	\N	Hector is scheduled to conduct the assessments in end of November.	Active	ERA for two properties developed and managed by Prometheus Real Estate Group in Mountain View, CA:\r\n The Hadley – 525-769 East Evelyn Road, Mountain View, CA, 94041\r\n The Dean – 458 San Antonio Road, Mountain View, CA, 94040\r\n\r\nInclusive price: $16,610	CIS	2022-11-27 08:44:12	2022-12-09 19:40:17	Proposal to Prometheus Real Estate Group - 10042022.pdf	doc02363020221017084224.pdf	\N	\N	\N	\N	\N
34	Braze	Security Assessment	Suzie Youd\r\nsusie.youd@braze.com	\N	Proposal Sent	1.\t330 W 34th St\r\n2.\tYes, a multi-tenant building\r\n3.\tWe occupy all of floors 16-18\r\n4.\tWe are a sub-tenant	CIS	2022-12-16 17:59:45	2022-12-16 17:59:45	\N	\N	\N	0	0	0	0
18	Build-A-Bear Workshop	Emergency Response Plan & Training	Mark Bartlett\r\nSenior Manager - Construction and Facilities\r\nBuild-A-Bear Workshop, Inc.\r\n1954 Innerbelt Business Center Drive\r\nSaint Louis, MO  63114\r\nT: 314-423-8000 ext. 5380\r\nF: 314-423-8188\r\nC: 314-724-0584\r\nmarkba@buildabear.com	All phases of work completed. Project closed.	Completed	Assessment of security measures and emergency infrastructure at BABW's new HQ in Saint Louis, authoring a facility-specific Emergency Response Plan, and training employees and special team members in emergency procedures.	CIS	2022-11-27 07:24:50	2022-12-16 17:55:57	Consulting Agreement for BABW 012722--signedCSG.pdf	Proposal to Build-A-Bear  - OCT2021.pdf	\N	10	0	0	0
8	Netherlands MvD	Private ATO & ATRR Programs (The Hague)	Robert Van Der Haas\r\nRavdHASI@protonmail.com\r\n\r\nEd Postimus\r\npsc.advies@protonmail.com	07/21/2021 - Robert requested that we update our agreement to $2600 per student for three classes (two ATO and 1 ATRR) for a total of 23 students. S2's gross will be $59,800. We will pay for IACSP memberships and cATO certifications for 13 students ($3,900). With estimated expenses for airfare and three weeks of travel, total expense costs are $13,500. Our net should be approx $45,000 for the three weeks of training.\r\n\r\n06/14/2021 - Will be contracted through Robert van Der Haas. Pricing quoted as follows:\r\n------\r\nATO Program (5-Day) Course\r\n•\tUS$2,600 per student\r\n•\t10 student minimum\r\n\r\nATRR Program (4-Day Course)\r\n•\tUS$2,300 per student\r\n•\t8 student minimum\r\no\tI calculated the pricing above based on 8 students since the audience for the ATRR is expected to be smaller than the ATO. However, if you and Ed feel 10 students should be a minimum, change our per student pricing to US$1,840.\r\n\r\ncATO Examination Review (1-Day)\r\n•\tUS$385 per student\r\n•\t10 student minimum\r\n------------\r\n\r\nEd has the budget approved, but is awaiting release of funds. He plans to run both programs consecutively in November.	Completed	Private ATO & ATRR Programs for his gang in The Hague. I haven't given him a firm quote yet, but will probably be $24,000 plus expenses estimated once dates are set and travel costs can be calculated.	S2	2021-06-03 12:52:08	2022-12-16 17:56:57	\N	\N	\N	0	0	0	0
35	Jack Berkey	Lincoln Avenue Capital - New Orleans	Jack Berkey - jberkey@lincolnavecap.com	Contact form was submitted on 1/18/23 by a Jack Berkey\r\nHector responded on 1/18/23 requesting a zoom meeting\r\nAs of 1/18/23 1205 waiting on response	New Lead	From client contact from - \r\nMessage\t\r\nHi Craig - I hope this note finds you well. I am reaching out to inquire about your security consultation services. I am inquiring as it relates to a 163 unit multifamily project based in New Orleans that is currently undergoing renovation. My team and I are trying to get the correct infrastructure in place during the design stages in order to try and reduce our expense of having a 24/7 full time security guard. Thank you in advance for your time. I look forward to connecting soon. Best, Jack Berkey	CIS	2023-01-18 17:07:34	2023-01-18 17:07:34	\N	\N	\N	0	0	0	0
33	Mary Institute and Saint Louis Country Day School	School Security & Emergency Readiness Assessment	Beth Miller\r\nbmiller@micds.org	Proposal sent on 12/16/2022\r\nTesting service rate increase to $275 per hour calculations	Proposal Sent	Security & emergency readiness assessment	CIS	2022-12-16 17:50:49	2023-01-26 15:54:12	Proposal to MICDS - 121662022.pdf	\N	\N	0	0	0	0
36	TestClient	TestProject	\N	\N	New Lead	\N	CIS	2023-01-26 13:50:13	2023-01-26 13:50:13	\N	\N	\N	0	0	0	0
\.


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: projecttracker
--

SELECT pg_catalog.setval('public.clients_id_seq', 36, true);


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: projecttracker
--

COPY public.failed_jobs (id, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: projecttracker
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: projecttracker
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_000000_create_users_table	1
2	2014_10_12_100000_create_password_resets_table	1
3	2019_08_19_000000_create_failed_jobs_table	1
4	2021_06_01_185559_create_client_table	1
5	2021_06_15_144927_add_upload_feilds_to_clients	2
6	2022_12_12_144729_add_rate_feilds_to_client_table	3
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: projecttracker
--

SELECT pg_catalog.setval('public.migrations_id_seq', 6, true);


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: projecttracker
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: projecttracker
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
3	KC Poulin	poulinkc@cisadmin.com	2021-06-03 14:02:14	$2y$10$P8vA4kMMulvxwI1o76Xl2Oa.5leOXmVjottFe27qdH7LLNtSAG5TS	\N	2021-06-03 14:02:14	2021-06-03 14:02:14
1	Richard Clark	richievc@gmail.com	2021-06-03 14:02:13	$2y$10$TETBzuocQV7U.EwZoIRW0.IYDK4LnWQYpGOsXqZhuvmBvV9aulR2C	\N	2021-06-03 14:02:13	2021-06-03 14:18:51
4	Jeff Ezell	ezelljt@kkpsecuritygroup.com	2021-06-03 14:02:14	$2y$10$SQlT/yGleZCGGrPfksYlyeH/v69NLNhyZxCihbq8R6LDRt4szYRtq	U968mrbJI3vgDrOoXSPEV6bhyL76S2Hy4EYmh4sT5hdpCyk5BcGnhMW8WASD	2021-06-03 14:02:14	2021-06-03 14:02:14
6	Ashley Casey	ashley@s2institute.com	2022-11-21 11:25:21	$2y$10$i2Cx5PzfZOeDpeGaRGB.T.6TI8ePnJsshwlwt0iTpo1TJ5uqZxNDu		2022-11-21 11:25:21	2022-11-22 15:26:34
5	Hector Rodriguez	rodrighb@cisworldservices.org	2022-11-21 11:24:24	$2y$10$fZULNLYUWoLk6SVg6uZC0.jO6wE8e8AWPM58qi1LvXg5aRgHeHIYW	11sGi4yg4k0XuSLZqvj0tzOhGC3bobOFtoFXpYwePLgh8ZXfU2ylMXJD37Bb	2022-11-21 11:24:24	2022-11-22 15:26:51
2	Craig Gundry	gundrycs@cisadmin.com	2021-06-03 14:02:13	$2y$10$VoaJR61vGx2nb70081BoSeftaC/JlMq2lDxzNYVkgVlQu7UE4gdJO	zoA0y73lGAuw4qC3Rz3nB8KhIF5Q7xymx3bceyR2xLGd5d9Slctfd0yjBtHC	2021-06-03 14:02:13	2022-11-22 15:23:14
7	Chris Jones	jonesy@cisworldservices.org	\N	$2y$10$.kolArc6/VJVCS1YRRm3Lu5T8FttqoZVGFaY9lBHeALB1zWu39OeS	\N	2023-01-26 18:53:48	2023-01-26 18:53:48
\.


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: projecttracker
--

SELECT pg_catalog.setval('public.users_id_seq', 7, true);


--
-- Name: clients clients_pkey; Type: CONSTRAINT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: projecttracker
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: client_name_idx; Type: INDEX; Schema: public; Owner: projecttracker
--

CREATE INDEX client_name_idx ON public.clients USING btree (client_name);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: projecttracker
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- Name: project_name_idx; Type: INDEX; Schema: public; Owner: projecttracker
--

CREATE INDEX project_name_idx ON public.clients USING btree (project_name);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

