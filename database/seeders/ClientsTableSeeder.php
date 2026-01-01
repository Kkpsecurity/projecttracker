<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding clients table with project data...');

        $clients = array (
  0 => 
  array (
    'id' => 6,
    'client_name' => 'Kohl\'s',
    'project_name' => 'Active Shooter Assessment of Kohl\'s Facilities',
    'poc' => 'N/A - All is going right now through Kohl\'s procurement hub.',
    'status' => 'Received word they did not want to proceed with our participation further in the RFP.',
    'quick_status' => 'Closed',
    'description' => 'Active shooter assessment of four Kohl\'s facilities including corporate headquarters, another office complex, one distribution center, and one retail center.',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-02 21:05:37',
    'updated_at' => '2021-06-16 12:24:53',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  1 => 
  array (
    'id' => 10,
    'client_name' => 'Precept Management',
    'project_name' => 'Private ATO Course (Cyprus)',
    'poc' => 'Nick
Precept Management Consultancy',
    'status' => 'Nick has agreed to pricing and dates (6th-10th September): $11,500 plus travel expenses.\\r\\n\\r\\nJust awaiting confirmation from his Cypriot government client. I marked it as Active since pricing and dates are set, but it is still pending final confirmation from the Govt.',
    'quick_status' => 'Closed',
    'description' => 'Private ATO program for Cypriot security professionals. Location will most likely be Nicosia.',
    'corporate_name' => 'S2',
    'created_at' => '2021-06-03 13:04:16',
    'updated_at' => '2021-07-20 18:00:18',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  2 => 
  array (
    'id' => 14,
    'client_name' => 'Bombardier Aerospace',
    'project_name' => 'Active Shooter Assessment for Red Oak, TX Plant',
    'poc' => 'Gloria
514-297-4548',
    'status' => 'Left a voicemail seeking an update on 23 June.\\r\\n---------\\r\\nI spoke with the client on 6/15/2021. Client is eager to have an assessment completed of their Red Oak, TX plant. When speaking, I gave her a conservative budget estimate of $9,000. I am awaiting her approval after speaking with her management.',
    'quick_status' => 'Closed',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-16 12:31:21',
    'updated_at' => '2022-11-25 16:24:06',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  3 => 
  array (
    'id' => 1,
    'client_name' => 'eu-LISA',
    'project_name' => 'ATO Program (Strasbourg)',
    'poc' => 'CONTRACT POC: Anna Schmidt
EMAIL: aschmidt@infeurope.lu

EU-LISA POC: Jean-Pierre Zinzen
EMAIL: Jean-Pierre.ZINZEN@EULISA.EUROPA.EU',
    'status' => 'On 31 May, dates were confirmed for private ATO program. Waiting for purchase order or contract to confirm everything.',
    'quick_status' => 'Closed',
    'description' => 'Private ATO course for eu-LISA security personnel in Strasbourg, France. Dates are now set for 11-15 October 2021.

Inclusive price is EUR 18.586,00',
    'corporate_name' => 'S2',
    'created_at' => '2021-06-02 20:28:18',
    'updated_at' => '2022-11-25 16:24:35',
    'file1' => 'eu-LISA_Proposal_SecurityTraining_06212021 (UPDATED).pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  4 => 
  array (
    'id' => 4,
    'client_name' => 'Invictus',
    'project_name' => 'Physical Security Plan for Cannabis Facility in MA',
    'poc' => 'Pat Miller',
    'status' => 'Sent Pat our proposal on 6/16/2021. Total estimate is $24,445.00.',
    'quick_status' => 'Closed',
    'description' => 'Develop a physical security plan and design for a company preparing for a MA cannabis cultivation license. May also include supervision of implementation and getting the security program underway after the license is approved.',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-02 20:50:37',
    'updated_at' => '2022-11-25 16:24:54',
    'file1' => 'pjiS0jKC0TLGHtGiZggTTWfP1LfZMwLp3OfUvUgW.doc',
    'file2' => 'Proposal to Fried Law Group - JUN2021.doc',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  5 => 
  array (
    'id' => 15,
    'client_name' => 'Barton County',
    'project_name' => 'Barton County School Threat Assessment Program',
    'poc' => 'Sue Cooper
scooper@bartoncounty.org
Tel. (620) 793-1800',
    'status' => 'Proposal was sent on 23 June. I expect we will have a phone conference within the next few weeks to work out any details or changes needed for her grant applicaiton.',
    'quick_status' => 'Closed',
    'description' => 'Barton County is applying for the BJA STOP school violence grant program and needs a quote to develop a school threat assessment program for three rural school districts, located in Barton County, Kansas.',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-16 12:38:41',
    'updated_at' => '2022-11-25 16:25:07',
    'file1' => 'Proposal to Barton County - Threat Assessment Program - 06212021.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  6 => 
  array (
    'id' => 9,
    'client_name' => 'Swedish Civil Contingencies Agency (MSB)',
    'project_name' => 'Custom ATO Course (Stockholm)',
    'poc' => 'Petter Säterhed
Swedish National Police, The Swedish Civil Contingencies Agency (MSB)
petter.saterhed@msb.se',
    'status' => 'Communicated with Petter during the week of 29 May about doing a private ATO program for his group in Stockholm. He is examining budget on his end. I need to follow up by 07 June of I don\'t hear sooner.',
    'quick_status' => 'Closed',
    'description' => 'Custom ATO program in Stockholm for Swedish police assigned to protecting public locations. Will likely charge $13,000 plus travel expenses and try to schedule adjacent to another trip to minimize cost and travel time.',
    'corporate_name' => 'S2',
    'created_at' => '2021-06-03 13:00:47',
    'updated_at' => '2022-11-25 16:25:24',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  7 => 
  array (
    'id' => 2,
    'client_name' => 'European Parliament',
    'project_name' => 'Private ATO Coursea (Brussels)',
    'poc' => 'Mariana KRAJCOVA 
Administrative manager 
European Parliament 
Directorate-General for Security and Safety 
Directorate for Strategy and Resources 
Training Unit
BRU - SPINELLI 07D84 - Tel. +32 228 31472 
Cell phone: +32 470 89 34 72
mariana.krajcova@europarl.europa.eu 

www.europarl.europa.eu',
    'status' => 'Completing second course the week of 11/27/2022\\r\\nThird course scheduled for 6 to 10 February 2023',
    'quick_status' => 'Active',
    'description' => 'Private ATO course for European Parliament security personnel.

Contract amount: EUR  17.900,00 per session',
    'corporate_name' => 'S2',
    'created_at' => '2021-06-02 20:37:57',
    'updated_at' => '2022-11-27 08:35:20',
    'file1' => '2019-002 FWK CT signed.pdf',
    'file2' => 'Purchase order_2022_119.pdf',
    'file3' => 'Purchase order_2022_152.pdf',
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  8 => 
  array (
    'id' => 3,
    'client_name' => 'Engineering Matrix, Inc',
    'project_name' => 'NFPA 72 Risk Assessment for Pinellas School',
    'poc' => 'Greg Bowen
EMAIL: gregb@engmtx.com',
    'status' => 'Sent him an email for an update on 23 June.\\r\\n----------------\\r\\nSent contract on 01 June.',
    'quick_status' => 'Completed',
    'description' => 'Risk assessment for a new Pinellas school in the design process to meet new compliance requirements of NFPA 72.',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-02 20:43:09',
    'updated_at' => '2022-12-16 17:56:27',
    'file1' => 'CONSULTING AGREEMENT - Engineering Matrix Inc - 06012021.doc',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  9 => 
  array (
    'id' => 5,
    'client_name' => 'HAI Group',
    'project_name' => 'LE Guidelines Review',
    'poc' => 'JB Smith
HAI Group
189 Commerce Court, Cheshire CT 06410
Direct 203-272-8220, ext. 351 | Toll Free 800-873-0242 | Cell 757-812-8291
jsmith@housingcenter.com',
    'status' => 'Project started. Terri & KC are working on first sample of report to send to HAI.',
    'quick_status' => 'Completed',
    'description' => 'Expert review of HAI Group Guidelines. See proposal & contract for details.',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-02 21:02:34',
    'updated_at' => '2022-12-16 17:56:37',
    'file1' => 'Proposal to HAI Group  - Rev 02192021.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  10 => 
  array (
    'id' => 7,
    'client_name' => 'Waterton',
    'project_name' => 'Environmental Risk Assessment for The Amelia (MA)',
    'poc' => 'Nastassja Heintz-Janis
Nastassja.Heintz-Janis@waterton.com

William Aguiar
William.Aguiar@waterton.com',
    'status' => 'Completed.',
    'quick_status' => 'Completed',
    'description' => 'Environmental Risk Assessment for The Amelia (MA).

Inclusive Price: $6,965.00',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-02 21:10:08',
    'updated_at' => '2022-12-16 17:57:08',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  11 => 
  array (
    'id' => 11,
    'client_name' => 'The Greater Dayton School',
    'project_name' => 'The Greater Dayton School',
    'poc' => 'A.J. Stich 
The Greater Dayton School – Founding Principal 
10510 N Springboro Pike, Miamisburg OH 45342 
(937) 434-3095, extension 3351 
astich@greaterdayton.org

greaterdayton.org',
    'status' => 'Proposal sent on 23 June. They will advise is we advance to another interview.\\r\\n\\r\\nA.J. googled security consulting and we popped up.',
    'quick_status' => 'Closed',
    'description' => 'Below is a brief outline of the work that will need done:
•  Security schematic design - Working with architects to review security systems integrated into the architectural designs for the school.
•  School Security Technology - Help us secure innovative technology solutions to enhance school security.  Coordinate with security technology vendors to properly install/manage systems.
•  Master planning - Design a school security standard operating procedures master plan and manual, teacher/staff security planning training, advise us on security staffing.
Below are some details about the school:
•  Building - 5 levels, 100,000 sq. ft. (new construction)
•  Staff - 29 to start, building up to 60
•  Students - 120 to start, building up to 400',
    'corporate_name' => 'CIS',
    'created_at' => '2021-06-04 03:33:56',
    'updated_at' => '2022-11-25 16:23:32',
    'file1' => 'Proposal to The Greater Dayton School - 06172021.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  12 => 
  array (
    'id' => 19,
    'client_name' => 'Highway Transport',
    'project_name' => 'Security Assessment Project',
    'poc' => 'Rick Lusby
Vice President of Safety and Fleet Services
Direct \\t(865) 474-8010
Mobile \\t(865) 740-8046
RLusby@highwaytransport.com',
    'status' => 'All physical assessments are complete - Need to finish preparing oral ROF\\r\\nOral ROF delivery scheduled for 13 December, 15:30 by Zoom',
    'quick_status' => 'Active',
    'description' => 'Assessment of the Highway Transport Corporate Office and four other specified service center locations: Knoxville Service Center, Baton Rouge Service Center, Lake Charles Service Center, & Houston Service Center

Final billing upon completion: $28,940',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:31:51',
    'updated_at' => '2022-11-27 07:32:32',
    'file1' => 'Signed-Agreement.pdf',
    'file2' => 'Proposal to Highway Transport - Revised 08162022.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  13 => 
  array (
    'id' => 20,
    'client_name' => 'Hall & Evans, LLC',
    'project_name' => 'Castillo v STEM (Expert Witness)',
    'poc' => 'David M. Jones | Member
jonesd@hallevans.com
Tel: 303-628-3312
jonesd@hallevans.com',
    'status' => 'Report delivered on 11/24/2022\\r\\nWaiting for dates for deposition - Will need to assist in preparing for opposition expert deposition in December\\r\\nTrial is expected in February 2023\\r\\nWork completed to date: est. $65,000 - Presently owes us $40,000 in new retainers',
    'quick_status' => 'Active',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:36:49',
    'updated_at' => '2022-11-27 07:36:49',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  14 => 
  array (
    'id' => 21,
    'client_name' => 'Saint Philips Episcopal Church & School',
    'project_name' => 'Church and School Security Assessment',
    'poc' => 'Edward Diaz
Chief Operations Officer
Saint Philip’s Episcopal Church and School
1121 Andalusia Avenue
Coral Gables, Florida 33134
Phone (305) 444-6366
ediaz@saintphilips.net',
    'status' => 'Assessment scheduled 11-12 December - Hector & Craig participating\\r\\nNeed to write report immediately afterward',
    'quick_status' => 'Active',
    'description' => 'Assessment of church and school with written report of findings.  Inclusive billing upon delivery of the report: $16,290',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:45:08',
    'updated_at' => '2022-11-27 07:46:16',
    'file1' => '24990521_2_(MIADOCS)_St.PhilipsEpiscopal-CIS-Consulting-Agreement.pdf',
    'file2' => 'Proposal to Saint Philips Episcopal Church and School - 08122022.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  15 => 
  array (
    'id' => 17,
    'client_name' => 'West Coast University',
    'project_name' => 'WCU Assessment Project',
    'poc' => 'Rob Koran
rkoran@westcoastuniversity.edu
949-870-6501

David Tran
Cell: 479-739-2662
DaTran@westcoastuniversity.edu',
    'status' => 'Fort Richardson assessment is underway - Need to prepare oral ROF\\r\\nHector has scheduled three assessments in CA for the last week of November\\r\\nNeed to submit a schedule for the remainder of assessments in January-March',
    'quick_status' => 'Active',
    'description' => 'Physical assessment of security and safety conditions at the following 14 WCU facilities and parking lots. See contract and proposal.

Billed hourly and invoiced biweekly. Rates: $225 per hour for services and $162 per hour for travel',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:18:26',
    'updated_at' => '2022-11-27 07:48:35',
    'file1' => 'CIS Consulting Agreement for WCU - Campus Assessments-10192022.docx.pdf',
    'file2' => 'Proposal to West Coast University - 07102022.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  16 => 
  array (
    'id' => 23,
    'client_name' => 'Florida Department of Health',
    'project_name' => 'Active Shooter Response Train-the-Trainer Program',
    'poc' => 'Mary Register
Florida Department of Health
Division of Emergency Preparedness and Community Support
Bureau of Preparedness and Response
Training and Exercise Specialist
4052 Bald Cypress Way Bin A-23
Tallahassee, FL 32399
Desk:  850-245-4894 
Mobile:  850-445-9265
Mary.Register@FLHealth.gov',
    'status' => 'Proposal submitted on 11 November 2022',
    'quick_status' => 'Proposal Sent',
    'description' => 'Develop & Present trainer-the-trainer (T-o-T) program focusing on active shooter response for 50 designated Department employees over two days. Training will be conducted on-site at an FDOH facility in Orlando. See proposal for details.

If both T-o-T sessions are scheduled concurrently as two one-day courses, the total price of services is $24,150.',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:58:44',
    'updated_at' => '2022-11-27 07:59:11',
    'file1' => 'Proposal to Florida Department of Health - 11102022.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  17 => 
  array (
    'id' => 24,
    'client_name' => 'Oak Street Health',
    'project_name' => 'Security Assessments',
    'poc' => 'Pryce Williams
pryce.williams@oakstreethealth.com',
    'status' => 'Hector & Craig discussed the project on Zoom with 0n 14 November. They are refining a short list of facilities for assessment before we submit a proposal.',
    'quick_status' => 'New Lead',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 08:34:28',
    'updated_at' => '2022-11-27 08:34:28',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  18 => 
  array (
    'id' => 25,
    'client_name' => 'FLETC',
    'project_name' => 'Anti-Terrorism Officer Program',
    'poc' => 'David A. Saunders
Senior Instructor
Counterterrorism Division
Off:912-261-3672
Cell:912-944-7908
david.a.saunders@fletc.dhs.gov',
    'status' => 'Discussed conducting a private ATO program at FLETC on 10/3/2022. Last message to Saunders:\\r\\n-------------------\\r\\nRegarding costs, for private groups with a hosted venue site, pricing is based on $12,000 per course (5-days/40-hours) plus expenses. If it was International, there would be an added fee for travel time but not relevant with a short trip to GA. I can accommodate up to 25 students in an ATO course. \\r\\n\\r\\nSo if it were contracted, we’d estimate the expenses and provide an inclusive rate (probably somewhere around $15,000 including expenses for GA). If the arrangement was to price for individual tuition, we would probably suggest a pricing model at $1,500 per student and a 10 student minimum. If the host is confident they can deliver a larger audience, that price can be lowered. In the end, what matters is that we arrive at a minimum of $15,000 gross for the course.\\r\\n\\r\\nFor organizations that want to host multiple sessions, we adjust that pricing according to scale. For example, Amazon recently requested a series of six sessions and we adjusted the base price to $11,000 per week (excluding expenses).',
    'quick_status' => 'New Lead',
    'description' => NULL,
    'corporate_name' => 'S2',
    'created_at' => '2022-11-27 08:38:31',
    'updated_at' => '2022-11-27 08:38:31',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  19 => 
  array (
    'id' => 28,
    'client_name' => 'Tampa Hillsborough County Expressway Authority',
    'project_name' => 'Emergency Response Planning & Training',
    'poc' => 'Gary Holland
Toll Systems Manager
Tampa Hillsborough County Expressway Authority
1104 E. Twiggs Street, Suite 300
Tampa, Florida 33602
813.610.2423
gary.holland@tampa-xway.com',
    'status' => 'Need to schedule date for assessment in December.',
    'quick_status' => 'Active',
    'description' => 'Security and emergency readiness audit of the THEA office, physical security
analysis, development of a comprehensive all-hazard Emergency Response Plan (EMP) specific
to THEA’s facilities, and training Emergency Response Team members and THEA employees in
critical emergency response procedures.

Billed in Four Phases. Total Price: $19,825.',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 09:02:34',
    'updated_at' => '2022-11-27 09:04:20',
    'file1' => 'CIS Original Agreement Executed.pdf',
    'file2' => 'Proposal to Tampa Hillsborough County Expressway Authority - MAR2022.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  20 => 
  array (
    'id' => 29,
    'client_name' => 'Marysville Joint Unified School District',
    'project_name' => 'School Security Assessment & Program Improvement',
    'poc' => 'Bryan Williams
bwilliams@mjusd.k12.ca.us',
    'status' => 'Proposal sent on 11/5/2022',
    'quick_status' => 'Proposal Sent',
    'description' => 'Assessment of security and safety conditions at twenty-three MJUSD schools. See proposal for details.
Hourly billing: $225 | Travel: $175

Total Estimated cost: $122,000',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 09:32:07',
    'updated_at' => '2022-11-27 09:35:28',
    'file1' => 'Marysville Joint Unified School District Security Assessment Proposal - 2022.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  21 => 
  array (
    'id' => 30,
    'client_name' => 'Third Baptist Church of San Francisco',
    'project_name' => 'Church Security Assessment',
    'poc' => 'Jamie Muntner
mailto:jamie@thirdbaptist.org',
    'status' => 'Responded on 06 December. Haven\'t heard back yet.',
    'quick_status' => 'New Lead',
    'description' => 'Third Baptist Church of San Francisco would like a comprehensive security consultation for our property and operations.',
    'corporate_name' => 'CIS',
    'created_at' => '2022-12-09 12:08:15',
    'updated_at' => '2022-12-09 12:08:15',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  22 => 
  array (
    'id' => 22,
    'client_name' => 'Savannah Christian Preparatory School',
    'project_name' => 'School Security Assessment',
    'poc' => 'Jeff Plunk
Head of School
Tel. 912-721-1763
jplunk@savcps.com',
    'status' => 'Scheduled for January 30 and 31. Sent info request.',
    'quick_status' => 'Active',
    'description' => 'School security assessment with oral report of findings. Inclusive Price: $12,550',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:53:40',
    'updated_at' => '2022-12-09 12:10:55',
    'file1' => 'Proposal to SCPS  - 10262022.pdf',
    'file2' => 'Agreement-CIS-SCPS-11302022.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  23 => 
  array (
    'id' => 27,
    'client_name' => 'North Cobb Christian School',
    'project_name' => 'SPO Course & Active Shooter Tactical Training',
    'poc' => NULL,
    'status' => 'Need to prepare a proposal for Todd. Agreed on price of $11,000.',
    'quick_status' => 'New Lead',
    'description' => 'NCCS wants to conduct an SPO in the Summer of 2023. Todd was advised the price would be approximately $12,000 altogether including expenses. Course will be taught by Craig an Shannon (tactical).',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 08:55:19',
    'updated_at' => '2022-12-09 12:15:35',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  24 => 
  array (
    'id' => 31,
    'client_name' => 'West Coast University',
    'project_name' => 'REFERRAL - Window Film for WCU',
    'poc' => 'Scott McCutcheon
Emerald Coast Glass Protection
 p: 850-832-5859 
e: scott@glassprotectionconsulting.com',
    'status' => 'WCU wants Scott McCutcheon, Emerald Coast Glass Protection, to do an assessment of Richardson and start working on a solution for implementing our recommendations. I\'ll need to work with Scott on a referral fee for us once he gets the project underway.',
    'quick_status' => 'New Lead',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2022-12-09 13:10:54',
    'updated_at' => '2022-12-09 13:10:54',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  25 => 
  array (
    'id' => 32,
    'client_name' => 'Greystar',
    'project_name' => 'Property Assessments',
    'poc' => 'Sal Ariganello
SAL.ARIGANELLO@GREYSTAR.COM',
    'status' => 'Inquiry received on 09 December: "I am on the investment team at Greystar. We are looking to set up a meeting to discuss possible crime consultations for some of our properties on the East Coast."\\r\\n\\r\\nSent reply seeking a day and time to speak.',
    'quick_status' => 'New Lead',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2022-12-09 18:46:38',
    'updated_at' => '2022-12-09 18:46:38',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  26 => 
  array (
    'id' => 26,
    'client_name' => 'Prometheus Real Estate Group',
    'project_name' => 'Property Assessments for The Dean and The Hadley Properties',
    'poc' => NULL,
    'status' => 'Hector is scheduled to conduct the assessments in end of November.',
    'quick_status' => 'Active',
    'description' => 'ERA for two properties developed and managed by Prometheus Real Estate Group in Mountain View, CA:
 The Hadley – 525-769 East Evelyn Road, Mountain View, CA, 94041
 The Dean – 458 San Antonio Road, Mountain View, CA, 94040

Inclusive price: $16,610',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 08:44:12',
    'updated_at' => '2022-12-09 19:40:17',
    'file1' => 'Proposal to Prometheus Real Estate Group - 10042022.pdf',
    'file2' => 'doc02363020221017084224.pdf',
    'file3' => NULL,
    'project_services_total' => NULL,
    'project_expenses_total' => NULL,
    'final_services_total' => NULL,
    'final_billing_total' => NULL,
  ),
  27 => 
  array (
    'id' => 34,
    'client_name' => 'Braze',
    'project_name' => 'Security Assessment',
    'poc' => 'Suzie Youd
susie.youd@braze.com',
    'status' => NULL,
    'quick_status' => 'Proposal Sent',
    'description' => '1.\\t330 W 34th St
2.\\tYes, a multi-tenant building
3.\\tWe occupy all of floors 16-18
4.\\tWe are a sub-tenant',
    'corporate_name' => 'CIS',
    'created_at' => '2022-12-16 17:59:45',
    'updated_at' => '2022-12-16 17:59:45',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  28 => 
  array (
    'id' => 18,
    'client_name' => 'Build-A-Bear Workshop',
    'project_name' => 'Emergency Response Plan & Training',
    'poc' => 'Mark Bartlett
Senior Manager - Construction and Facilities
Build-A-Bear Workshop, Inc.
1954 Innerbelt Business Center Drive
Saint Louis, MO  63114
T: 314-423-8000 ext. 5380
F: 314-423-8188
C: 314-724-0584
markba@buildabear.com',
    'status' => 'All phases of work completed. Project closed.',
    'quick_status' => 'Completed',
    'description' => 'Assessment of security measures and emergency infrastructure at BABW\'s new HQ in Saint Louis, authoring a facility-specific Emergency Response Plan, and training employees and special team members in emergency procedures.',
    'corporate_name' => 'CIS',
    'created_at' => '2022-11-27 07:24:50',
    'updated_at' => '2022-12-16 17:55:57',
    'file1' => 'Consulting Agreement for BABW 012722--signedCSG.pdf',
    'file2' => 'Proposal to Build-A-Bear  - OCT2021.pdf',
    'file3' => NULL,
    'project_services_total' => 10.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  29 => 
  array (
    'id' => 8,
    'client_name' => 'Netherlands MvD',
    'project_name' => 'Private ATO & ATRR Programs (The Hague)',
    'poc' => 'Robert Van Der Haas
RavdHASI@protonmail.com

Ed Postimus
psc.advies@protonmail.com',
    'status' => '07/21/2021 - Robert requested that we update our agreement to $2600 per student for three classes (two ATO and 1 ATRR) for a total of 23 students. S2\'s gross will be $59,800. We will pay for IACSP memberships and cATO certifications for 13 students ($3,900). With estimated expenses for airfare and three weeks of travel, total expense costs are $13,500. Our net should be approx $45,000 for the three weeks of training.\\r\\n\\r\\n06/14/2021 - Will be contracted through Robert van Der Haas. Pricing quoted as follows:\\r\\n------\\r\\nATO Program (5-Day) Course\\r\\n•\\tUS$2,600 per student\\r\\n•\\t10 student minimum\\r\\n\\r\\nATRR Program (4-Day Course)\\r\\n•\\tUS$2,300 per student\\r\\n•\\t8 student minimum\\r\\no\\tI calculated the pricing above based on 8 students since the audience for the ATRR is expected to be smaller than the ATO. However, if you and Ed feel 10 students should be a minimum, change our per student pricing to US$1,840.\\r\\n\\r\\ncATO Examination Review (1-Day)\\r\\n•\\tUS$385 per student\\r\\n•\\t10 student minimum\\r\\n------------\\r\\n\\r\\nEd has the budget approved, but is awaiting release of funds. He plans to run both programs consecutively in November.',
    'quick_status' => 'Completed',
    'description' => 'Private ATO & ATRR Programs for his gang in The Hague. I haven\'t given him a firm quote yet, but will probably be $24,000 plus expenses estimated once dates are set and travel costs can be calculated.',
    'corporate_name' => 'S2',
    'created_at' => '2021-06-03 12:52:08',
    'updated_at' => '2022-12-16 17:56:57',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  30 => 
  array (
    'id' => 35,
    'client_name' => 'Jack Berkey',
    'project_name' => 'Lincoln Avenue Capital - New Orleans',
    'poc' => 'Jack Berkey - jberkey@lincolnavecap.com',
    'status' => 'Contact form was submitted on 1/18/23 by a Jack Berkey\\r\\nHector responded on 1/18/23 requesting a zoom meeting\\r\\nAs of 1/18/23 1205 waiting on response',
    'quick_status' => 'New Lead',
    'description' => 'From client contact from - 
Message\\t
Hi Craig - I hope this note finds you well. I am reaching out to inquire about your security consultation services. I am inquiring as it relates to a 163 unit multifamily project based in New Orleans that is currently undergoing renovation. My team and I are trying to get the correct infrastructure in place during the design stages in order to try and reduce our expense of having a 24/7 full time security guard. Thank you in advance for your time. I look forward to connecting soon. Best, Jack Berkey',
    'corporate_name' => 'CIS',
    'created_at' => '2023-01-18 17:07:34',
    'updated_at' => '2023-01-18 17:07:34',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  31 => 
  array (
    'id' => 33,
    'client_name' => 'Mary Institute and Saint Louis Country Day School',
    'project_name' => 'School Security & Emergency Readiness Assessment',
    'poc' => 'Beth Miller
bmiller@micds.org',
    'status' => 'Proposal sent on 12/16/2022\\r\\nTesting service rate increase to $275 per hour calculations',
    'quick_status' => 'Proposal Sent',
    'description' => 'Security & emergency readiness assessment',
    'corporate_name' => 'CIS',
    'created_at' => '2022-12-16 17:50:49',
    'updated_at' => '2023-01-26 15:54:12',
    'file1' => 'Proposal to MICDS - 121662022.pdf',
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
  32 => 
  array (
    'id' => 36,
    'client_name' => 'TestClient',
    'project_name' => 'TestProject',
    'poc' => NULL,
    'status' => NULL,
    'quick_status' => 'New Lead',
    'description' => NULL,
    'corporate_name' => 'CIS',
    'created_at' => '2023-01-26 13:50:13',
    'updated_at' => '2023-01-26 13:50:13',
    'file1' => NULL,
    'file2' => NULL,
    'file3' => NULL,
    'project_services_total' => 0.0,
    'project_expenses_total' => 0.0,
    'final_services_total' => 0.0,
    'final_billing_total' => 0.0,
  ),
);

        foreach ($clients as $client) {
            // Skip if client with this ID already exists
            if (!DB::table('clients')->where('id', $client['id'])->exists()) {
                DB::table('clients')->insert($client);
            }
        }

        $this->command->info('✅ Seeded ' . count($clients) . ' clients successfully');
    }
}