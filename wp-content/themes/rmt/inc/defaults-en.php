<?php
/**
 * English default content. Mirrors the structure of rmt_defaults();
 * repeater rows are matched by index. Stored as *_en keys.
 *
 * @package RMT
 */

defined( 'ABSPATH' ) || exit;

/**
 * English translations of the default content.
 *
 * @return array
 */
function rmt_defaults_en() {
	return array(
		'general'  => array(
			'company_name' => 'Rafal Al Madinah Contracting Est.',
			'short_name'   => 'Rafal Al Madinah',
			'tagline'      => 'Building cleaning & façade specialists since 2003',
			'whatsapp_msg' => 'Hello, I would like to ask about your services and request a quotation.',
			'address'      => 'Al Siteen Street, Al Malaz District, Riyadh, Kingdom of Saudi Arabia',
			'street'       => 'Al Siteen Street, Al Malaz',
			'city'         => 'Riyadh',
			'region'       => 'Riyadh Province',
			'po_box'       => 'P.O. Box 28597, Riyadh 11447',
			'hours'        => 'Saturday – Thursday: 8:00 AM – 6:00 PM',
		),

		'home'     => array(
			'hero_badge'     => 'Since 1424 AH – 2003',
			'hero_title'     => 'Building & Tower Façade Cleaning Company in Riyadh',
			'hero_text'      => 'Since 2003, Rafal Al Madinah Contracting has delivered glass façade, building and high-rise cleaning using rope access, alongside tower glass maintenance, residential compound cleaning, sofa and upholstery cleaning, pest control, and air-duct and central-kitchen exhaust cleaning for hotels and restaurants — serving government entities and the private sector in Riyadh and across Saudi Arabia. Our trained crews and certified safety equipment follow internationally recognised rope-access standards.',
			'btn_quote'      => 'Request a Quote',
			'btn_whatsapp'   => 'Chat on WhatsApp',
			'stats'          => array(
				array( 'label' => 'Years of experience' ),
				array( 'label' => 'Specialised services' ),
				array( 'label' => 'Government & private clients' ),
				array( 'label' => 'Countries: KSA & Bahrain' ),
			),
			'services_title' => 'Our Core Services',
			'services_text'  => 'Complete cleaning and maintenance solutions — at height and inside your facility.',
			'why_title'      => 'Why Rafal Al Madinah?',
			'why'            => array(
				array( 'title' => '20+ years of experience', 'text' => 'More than two decades in cleaning and contracting across Saudi Arabia.' ),
				array( 'title' => 'Trained, safe crews', 'text' => 'Teams trained in rope-access work to international safety standards.' ),
				array( 'title' => 'Trusted by government', 'text' => 'Cleaning contracts delivered for major entities such as Riyadh and Dammam Municipalities.' ),
				array( 'title' => 'No scaffolding needed', 'text' => 'Scaffold-free solutions that save our clients time and cost.' ),
				array( 'title' => 'Reliable quality', 'text' => 'On-time delivery and consistent quality on every project.' ),
			),
			'trust_title'    => 'Trusted by Government & Private Sector',
			'trust_text'     => 'We are proud of the trust placed in us by leading government bodies, companies, hotels and hospitals inside and outside the Kingdom.',
			'projects_title' => 'From Our Portfolio',
			'projects_text'  => 'Real footage from façade and tower cleaning projects carried out by our crews.',
			'cta_title'      => 'Does your building or tower need cleaning?',
			'cta_text'       => 'Contact us today for a site inspection and a competitive quotation — in Riyadh or anywhere in the Kingdom.',
			'faq_title'      => 'Frequently Asked Questions',
			'faq'            => array(
				array( 'q' => 'Do you need scaffolding to clean tower façades?', 'a' => 'No. We use rope access, which saves time and cost compared with scaffolding and cranes, while complying with international safety standards.' ),
				array( 'q' => 'Do you offer periodic cleaning contracts?', 'a' => 'Yes. We offer monthly, annual or one-off contracts for government entities, companies, residential and commercial compounds, with periodic reports on completed work.' ),
				array( 'q' => 'Which cities do you serve?', 'a' => 'We are based in Al Malaz, Riyadh, and serve all regions of Saudi Arabia. We have also completed projects in the Kingdom of Bahrain.' ),
				array( 'q' => 'How do I get a quotation?', 'a' => 'Fill in the quotation form or contact us directly by phone or WhatsApp on 0505203019. We will arrange a site visit and send you our offer.' ),
			),
		),

		'about'    => array(
			'title'        => 'About Rafal Al Madinah Contracting',
			'intro'        => 'Rafal Al Madinah Contracting is a Saudi establishment founded in 1424 AH (2003), specialising in cleaning services for the government and private sectors across the Kingdom of Saudi Arabia. We started with building cleaning and have grown into a trusted name in glass façade and tower cleaning, as well as maintenance and painting works using rope access.',
			'mission'      => 'Our clients’ trust is the secret of our success and continuity. We constantly strengthen that trust through honest, hard work, by training our people, and by making sure our services exceed client expectations on every project.',
			'vision'       => 'To excel in delivering the best cleaning and contracting services, keeping pace with the latest techniques and equipment in the field within the Kingdom.',
			'values'       => array(
				array( 'title' => 'Safety first', 'text' => 'We follow rope-access standards and use certified protective equipment.' ),
				array( 'title' => 'Quality', 'text' => 'Consistent results our clients can rely on.' ),
				array( 'title' => 'Commitment', 'text' => 'We respect deadlines and contract terms.' ),
				array( 'title' => 'Continuous improvement', 'text' => 'Ongoing crew training and equipment upgrades.' ),
			),
			'safety_title' => 'Safety Standards & Accreditations',
			'safety_text'  => 'Our team works to the standards of international associations specialising in rope access, lifting equipment and safety at height, such as IRATA, LEEA and DROPS.',
			'certs'        => array(
				array( 'desc' => 'Industrial rope access' ),
				array( 'desc' => 'Lifting equipment' ),
				array( 'desc' => 'Dropped-object prevention' ),
			),
			'timeline'     => array(
				array( 'year' => '2003', 'text' => 'Founded in Riyadh, starting with building cleaning services.' ),
				array( 'year' => 'Specialisation', 'text' => 'Focused on glass façade and tower cleaning using rope access.' ),
				array( 'year' => 'Government sector', 'text' => 'Cleaning contracts for Riyadh and Dammam Municipalities.' ),
				array( 'year' => 'Today', 'text' => 'Integrated services across Saudi Arabia and in the Kingdom of Bahrain.' ),
			),
		),

		'services' => array(
			'title'     => 'Cleaning & Maintenance Services for Buildings and Towers',
			'intro'     => 'We provide a complete range of cleaning and maintenance services for commercial, government and residential buildings, specialising in work at height using rope access — saving time and cost compared with scaffolding and cranes.',
			'cats_note' => 'Our services fall into three main categories: façades & work at height, interior cleaning, and restaurants, hotels & facilities.',
			'items'     => array(
				array( 'title' => 'Tower Glass Façade Cleaning', 'short' => 'Professional cleaning of tower and commercial building façades that restores and protects the glass shine.', 'text' => 'We clean the glass façades of towers, commercial buildings, hotels and hospitals using safe products that do not harm glass or frames, removing dust, stains, rain marks and deposits. Periodic cleaning contracts are available to suit Riyadh’s dusty climate.' ),
				array( 'title' => 'Rope-Access Building Façade Cleaning', 'short' => 'Safe cleaning of stone, cladding and aluminium façades at height.', 'text' => 'We clean stone, marble, cladding, aluminium and curtain-wall façades, removing built-up dust and stubborn stains to restore the building’s original appearance and protect its value.' ),
				array( 'title' => 'Tower Glass Maintenance & Replacement', 'short' => 'Repair and replacement of damaged glazing without costly scaffolding.', 'text' => 'We inspect and replace cracked or broken façade glass and treat sealing and silicone, using rope access to reach any height without disrupting building operations.' ),
				array( 'title' => 'Rope-Access Tower & Building Painting', 'short' => 'Fast, high-quality exterior painting for towers and high-rise buildings.', 'text' => 'Exterior painting and façade renovation for high-rise buildings using rope access — fast delivery at a lower cost than conventional methods.' ),
				array( 'title' => 'Residential Compound, Apartment & Chalet Cleaning', 'short' => 'Comprehensive general cleaning for compounds and units — periodic or one-off.', 'text' => 'Comprehensive general cleaning for residential compounds, buildings, apartments and chalets, including corridors, entrances and common areas, on monthly, annual or one-off contracts.' ),
				array( 'title' => 'Sofa, Upholstery & Carpet Cleaning', 'short' => 'Deep steam cleaning for sofas, carpets, curtains and mattresses — stains and odours removed.', 'text' => 'We clean sofas and upholstery for homes, hotels and offices using steam and deep-extraction machines, removing stains, dust, bacteria and odours without damaging fabrics. The service covers all sofa types, carpets and rugs, curtains, mattresses, and office and hall chairs.' ),
				array( 'title' => 'Pest Control & Fumigation', 'short' => 'Complete insect and rodent control with products safe for families and staff.', 'text' => 'We provide pest and rodent control for homes, residential compounds, restaurants, hotels and warehouses using safe, approved products under the supervision of trained technicians. We treat cockroaches, bed bugs, termites, ants, rodents and flying insects, with preventive programmes and a service guarantee.' ),
				array( 'title' => 'Central A/C Duct Cleaning', 'short' => 'Cleaning and sanitising of central A/C ducts for cleaner air and better cooling efficiency.', 'text' => 'With constant A/C use in the Kingdom, dust and mould build up inside air ducts, affecting air quality and raising power consumption. We clean and sanitise ducts with specialist equipment — rotary brushes and high-pressure extraction — including vents and filters, for offices, hotels, hospitals, and commercial and residential complexes.' ),
				array( 'title' => 'Central Kitchen Exhaust Cleaning for Hotels & Restaurants', 'short' => 'Removal of grease build-up from hoods, ducts and filters to reduce fire risk.', 'text' => 'Grease build-up in commercial kitchen hoods and exhaust ducts is a leading cause of fires. We clean central kitchen exhaust systems — hoods, filters, ducts and fans — for hotels, restaurants, hospitals and central kitchens, helping you comply with Civil Defence and municipal safety requirements. We can work outside operating hours and provide a report after every visit.' ),
				array( 'title' => 'Ship & Rig Repair and Maintenance', 'short' => 'Repair and maintenance for ships, rigs and industrial sites in high or hard-to-reach areas.', 'text' => 'Repair and maintenance works for ships, drilling rigs and industrial facilities that require access to high or difficult areas.' ),
				array( 'title' => 'Cleaning Contracts for Government & Corporate Clients', 'short' => 'Long-term cleaning and maintenance contracts with a clear work plan and regular reports.', 'text' => 'We provide long-term cleaning and maintenance contracts for government entities, companies and commercial complexes, with a clear work plan and periodic reports on completed work.' ),
			),
		),

		'projects' => array(
			'title' => 'Our Work',
			'intro' => 'A selection of façade and tower cleaning projects completed by our crews in Riyadh.',
			'items' => array(
				array( 'title' => 'Rope-access glass façade cleaning', 'place' => 'Riyadh' ),
				array( 'title' => 'Commercial tower cleaning', 'place' => 'Riyadh' ),
				array( 'title' => 'Rope-access team on a glass tower', 'place' => 'Riyadh' ),
				array( 'title' => 'Office tower façade cleaning', 'place' => 'Riyadh' ),
				array( 'title' => 'Full crew on a building façade', 'place' => 'Riyadh' ),
				array( 'title' => 'Hotel façade cleaning', 'place' => 'Riyadh' ),
				array( 'title' => 'Multi-storey building façade cleaning', 'place' => 'Riyadh' ),
			),
		),

		'clients'  => array(
			'title'   => 'Our Clients & Partners',
			'intro'   => 'For more than 20 years, Rafal Al Madinah Contracting has delivered cleaning and façade projects for a wide range of government entities, companies, hotels and hospitals in Saudi Arabia and abroad.',
			'outside' => 'Kingdom of Bahrain.',
			'items'   => array(
				array( 'name' => 'Riyadh Region Municipality' ),
				array( 'name' => 'Dammam Region Municipality' ),
				array( 'name' => 'Al Rajhi Complexes' ),
				array( 'name' => 'Al Faisaliah Group' ),
				array( 'name' => 'Nesma & Partners' ),
				array( 'name' => 'Alpha Company' ),
				array( 'name' => 'Riyad Bank' ),
				array( 'name' => 'Novotel Hotel' ),
				array( 'name' => 'Anan Hotel' ),
				array( 'name' => 'Ewaa Al Muhaidib Hotel' ),
				array( 'name' => 'Rio Hotel' ),
				array( 'name' => 'Al Hammadi Hospitals' ),
			),
		),

		'contact'  => array(
			'title'   => 'Contact Us & Request a Quote',
			'intro'   => 'We are happy to answer your questions and provide a site inspection and quotation for your project. Fill in the form or reach us directly by phone or WhatsApp.',
			'success' => 'Thank you for contacting us. Your request has been received and our team will get back to you shortly.',
		),

		'seo'      => array(
			'home_title'     => 'Façade Cleaning Company in Riyadh | Rafal Al Madinah',
			'home_desc'      => 'Rope-access glass façade & tower cleaning, sofa cleaning, pest control, A/C duct and kitchen exhaust cleaning for hotels and restaurants in Riyadh and KSA.',
			'about_title'    => 'About Us | Rafal Al Madinah Contracting – Since 2003',
			'about_desc'     => 'Rafal Al Madinah Contracting is a Saudi cleaning company founded in 2003, serving government and private clients with façade and tower cleaning.',
			'services_title' => 'Façade & Tower Cleaning Services in Riyadh | Rafal',
			'services_desc'  => 'Glass façade cleaning, rope-access building cleaning, tower glass maintenance, compound cleaning, sofa cleaning, pest control and duct cleaning in Riyadh.',
			'clients_title'  => 'Our Clients | Trusted Cleaning Company in KSA – Rafal',
			'clients_desc'   => 'Clients of Rafal Al Madinah include Riyadh and Dammam Municipalities, Al Faisaliah Group, hotels, hospitals and banks in Saudi Arabia and Bahrain.',
			'contact_title'  => 'Contact Us | Façade Cleaning Quote in Riyadh – Rafal',
			'contact_desc'   => 'Contact Rafal Al Madinah Contracting, Al Siteen St, Al Malaz, Riyadh. Mobile: 0505203019. Request an inspection and a façade cleaning quote.',
			'keywords'       => 'facade cleaning Riyadh, glass facade cleaning, rope access cleaning, tower cleaning, cleaning company Riyadh, tower glass maintenance, rope access painting, compound cleaning, government cleaning contracts, IRATA rope access, cleaning company Saudi Arabia, sofa cleaning Riyadh, carpet cleaning, pest control Riyadh, AC duct cleaning, kitchen exhaust cleaning, restaurant hood cleaning',
		),

		'footer'   => array(
			'about'     => 'A Saudi establishment specialising in rope-access glass façade and tower cleaning, plus complete cleaning services for the government and private sectors since 2003.',
			'copyright' => 'All rights reserved to Rafal Al Madinah Contracting Est.',
			'dev_text'  => 'Website developed by Dynamic Company team',
		),
	);
}
