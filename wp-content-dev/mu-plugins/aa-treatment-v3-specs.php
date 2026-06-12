<?php
/**
 * Per-page content specs for the treatment-page design v3 rollout.
 *
 * Each entry feeds rehab_build_treatment_v3() (aa-block-builders.php), which
 * supplies the shared sections; specs carry only condition-specific copy.
 * Trigger: /?rehab_oneshot=rebuild-treatment-v3&id=<page_id> (zz-oneshot.php).
 *
 * Copy rules (approved design, June 2026): no em dashes; CTA wording is
 * "Talk with admissions"; ~5 items per symptom list; phase paragraphs come in
 * pairs (the list renders after the second paragraph); quotes are attributed
 * to The Diamond Rehab Team, never invented named people.
 *
 * Spec keys:
 *   hero       — eyebrow, headline, lede, stat3Label (stats 1-2 shared)
 *   signs      — heading, subheading, card1Title, card1Items[], card2Title, card2Items[]
 *   holistic   — eyebrow, heading, body ("\n\n" between paragraphs)
 *   phases     — heading, items[3] (phase, label, h3, paragraphs[2], listItems[3], asideQuote, asideMetaLabel, asideMetaValue)
 *   inpatient  — body (optional heading/eyebrow overrides)
 *   prose      — heading, paragraphs[] (the SEO explainer)
 *   programTag — video-reel card label, e.g. "ice program"
 *   faqCptIds  — existing FAQ CPT ids to keep
 *   faqNew     — [ [question, answer], ... ] created in the FAQ CPT on rebuild
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function rehab_treatment_v3_specs(): array {
	return [
		867 => [
			'slug' => 'ice',
			'programTag' => 'ice program',
			'hero' => [
				'eyebrow' => 'Ice addiction treatment · Hua Hin',
				'headline' => 'Break free from ice, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medical detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating stimulant addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Ice dependence changes people visibly, in body and behaviour. These are the patterns families notice first. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of ice addiction',
				'card1Items' => [
					'Severe weight loss and loss of appetite',
					'Insomnia and memory problems',
					'Skin problems, dental decay and irregular heart rate',
					'Dishonesty, stealing and loss of motivation',
					'Increased aggression and volatile behaviour',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Intense cravings and impulsive urges to use',
					'A sharp drop in mood as dopamine levels fall',
					'Difficulty feeling pleasure or staying motivated',
					'Heightened sensitivity to stress, anger and depression',
					'Acute symptoms severe enough to need supervision',
				],
			],
			'holistic' => [
				'eyebrow' => 'A stubborn addiction',
				'heading' => 'A holistic approach to one of the hardest dependencies',
				'body' => "Ice is one of the most treatment-resistant addictions there is. Methamphetamine alters the brain's judgment and impulse control, which is why willpower alone so rarely works. Our program treats the dependency and the reasons beneath it at the same time.\n\nYou'll move through medically supervised detox into one-to-one counselling, behavioural therapy, exercise and mindfulness practice, all inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of ice recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised start',
						'paragraphs' => [
							'Ice withdrawal can be intense, and in some cases severe enough to put you and those around you at risk.',
							'Our clinical team begins with a comprehensive evaluation, then supervises your detox around the clock, with psychological support and appropriate medication to ease withdrawal while your mind and body adjust to functioning without meth.',
						],
						'listItems' => [
							'Comprehensive medical evaluation on arrival',
							'24-hour clinical supervision and support',
							'Medication to reduce withdrawal symptoms',
						],
						'asideQuote' => '"Acute ice withdrawal is not something to face alone. A supervised detox keeps you safe and makes the first weeks bearable."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the core of the addiction',
						'paragraphs' => [
							'Quitting ice is not just a physical battle. Meth reshapes the brain regions that govern impulse control, which is why cravings can override good intentions.',
							'Through one-to-one counselling and group work, you will get to the root psychological issues beneath the addiction and learn the social triggers and mental cues that drive use, so they lose their power over you.',
						],
						'listItems' => [
							'One-to-one counselling with addiction specialists',
							'Evidence-based behavioural therapy',
							'Understanding your triggers and mental cues',
						],
						'asideQuote' => '"Lasting recovery starts when you understand the patterns underneath the drug, not just the drug itself."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after rehab',
						'paragraphs' => [
							'Healing the mind and body together is what makes recovery hold.',
							'Exercise, mindfulness practice and structured routine become part of your day, and before you leave we design a tailor-made relapse prevention plan, so you re-enter the world with practical strategies for staying clean for good.',
						],
						'listItems' => [
							'Exercise and mindfulness woven into daily life',
							'A tailor-made relapse prevention plan',
							'Practical strategies for life after treatment',
						],
						'asideQuote' => '"Recovery is a life-long journey. Our job is to make sure you leave with the tools to keep meth out of your life for good."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to quit ice at home means staying surrounded by the people, places and routines that enable use. Residential treatment removes those triggers entirely and replaces them with a peaceful, structured environment and 24-hour care, so all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider ice rehab?',
				'paragraphs' => [
					'Ice, also known as crystal methamphetamine, is a powerful stimulant and one of the most damaging drugs in circulation. Chronic use takes a visible toll on the body, from severe weight loss and dental decay to insomnia, memory loss and an irregular heart rate, while quietly reshaping how the brain works.',
					"What makes ice so addictive is what it does to the brain's chemistry. Methamphetamine floods the reward system with dopamine, and when use stops, dopamine levels plummet, making it hard to stay motivated or find pleasure in ordinary life. It also alters the regions of the brain that control impulses and judgment, which is why cravings so often win against willpower alone.",
					'Over time the changes spread beyond the body and into behaviour. Many families notice dishonesty, stealing, loss of motivation, aggression or volatility long before they understand the cause. By this stage, ice addiction has become extremely resistant to treatment, and attempting to quit without medical supervision can be dangerous and sharply raises the risk of relapse. Research shows that an inpatient program is the single most effective way to treat crystal meth addiction.',
					"Acknowledging the problem takes real courage, and you don't have to travel the path to recovery on your own. If ice has taken hold of your life or the life of someone you love, a quiet, confidential conversation with our team is a good place to start.",
				],
			],
			'faqCptIds' => [ 32, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Why is ice addiction so hard to quit without help?',
					'answer' => "Methamphetamine alters the brain's ability to make rational decisions and control impulses, and when use stops, dopamine levels fall sharply, draining motivation and the ability to feel pleasure. That combination makes cravings very hard to resist alone. A structured residential program keeps you accountable, removes daily triggers and gives you round-the-clock support through the hardest weeks.",
				],
				[
					'question' => 'Do I need a medical detox for ice withdrawal?',
					'answer' => 'In many cases, yes. Acute ice withdrawal can be intense, and occasionally severe enough that going through it unsupervised is unsafe. During your initial consultation our clinicians carry out a comprehensive evaluation to determine whether a detox is needed. If it is, you will be supervised by our qualified clinical team throughout, with psychological support and appropriate medication to ease withdrawal symptoms.',
				],
			],
		],
		1064 => [
			'slug' => 'alcohol',
			'programTag' => 'alcohol program',
			'hero' => [
				'eyebrow' => 'Alcohol addiction treatment · Hua Hin',
				'headline' => 'Put down the bottle for good, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medically supervised detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating alcohol addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Alcohol dependence rarely announces itself. These are the patterns families notice first. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of alcohol addiction',
				'card1Items' => [
					'Blackouts and short-term memory loss',
					'Irritability and mood swings without a drink',
					'Making excuses to drink, to relax or to cope with stress',
					'Drinking secretly or downplaying how much',
					'Choosing alcohol over work, family and obligations',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Shaking hands, sweating, nausea and headaches',
					'Anxiety and insomnia, sometimes within six hours of the last drink',
					'Racing heart and high blood pressure',
					'Fever, confusion and hallucinations',
					'Seizures and delirium tremens, which is why detox must be medically supervised',
				],
			],
			'holistic' => [
				'eyebrow' => 'A complex addiction',
				'heading' => 'A holistic approach to the most familiar addiction',
				'body' => "Alcohol is legal, social and available almost everywhere, which is exactly why dependency so often creeps up unnoticed. Tolerance builds over years of seemingly controlled drinking, and what began as a way to relax or sleep becomes something the body and mind insist on. Willpower alone rarely undoes that, especially when drinking is also masking anxiety, depression or stress.\n\nOur program treats the whole picture, not just the bottle. A medically supervised detox clears alcohol safely, evidence-based therapy gets to the root of why drinking took hold, and fitness, mindfulness and relapse prevention rebuild daily life, all within a calm coastal residence in Hua Hin capped at twelve clients.",
			],
			'phases' => [
				'heading' => 'Three pillars of alcohol recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised start',
						'paragraphs' => [
							'Alcohol is one of the few substances where stopping suddenly can be dangerous in itself.',
							'Withdrawal can begin within six hours of the last drink, from shaking hands, sweating and nausea to fever, hallucinations and, in severe cases, seizures. Our doctors and nurses manage the process around the clock, easing symptoms with medication where needed so detox is as safe and comfortable as it can be.',
						],
						'listItems' => [
							'24/7 medical supervision',
							'Medication to ease withdrawal',
							'A private, calm environment',
						],
						'asideQuote' => '"No one should face alcohol withdrawal alone. Our medical team watches over every detox around the clock, so the most dangerous days are also the safest."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the root of drinking',
						'paragraphs' => [
							'Dependency rarely starts with alcohol itself. It starts with whatever alcohol is being used to manage.',
							'Through cognitive behavioural therapy, one-to-one counselling and small group sessions, our psychologists work with you to understand why drinking took hold, whether that is stress, anxiety, depression or simple habit, and to build healthier ways of coping that last beyond treatment.',
						],
						'listItems' => [
							'Cognitive behavioural therapy',
							'One-to-one counselling',
							'Small group therapy sessions',
						],
						'asideQuote' => '"Detox clears the alcohol. Therapy is where we work out why it was there in the first place."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Rebuilding a life without alcohol',
						'paragraphs' => [
							'Staying sober inside a rehab is one thing. Staying sober at home, around the old triggers, is the real work.',
							'Fitness training, mindfulness meditation and structured relapse prevention restore physical health and daily routine, and a personal aftercare plan keeps support in place long after you leave Hua Hin.',
						],
						'listItems' => [
							'Fitness and mindfulness practice',
							'Relapse prevention planning',
							'Structured aftercare back home',
						],
						'asideQuote' => '"Recovery has to survive the flight home. Everything we do here is built around that."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'A home detox is rarely effective for heavy drinking, and it can be genuinely dangerous. Residential treatment in Thailand removes you from the routines, social pressure and stress that fuel drinking, and puts a medical team beside you for the days when stopping is hardest.',
			],
			'prose' => [
				'heading' => 'Is it time to consider alcohol rehab?',
				'paragraphs' => [
					'Alcohol is one of the most widely abused and most addictive substances in the world, and because it is legal and available almost everywhere, the danger of frequent drinking is easy to underestimate. Alcohol dependence is the point at which drinking stops being a choice: the body has adapted to regular alcohol, tolerance has risen, and cutting back produces cravings, irritability or physical withdrawal rather than relief.',
					'The addiction works on two levels. Chemically, alcohol depresses the central nervous system, and with regular heavy use the brain compensates by running in overdrive, which is why stopping suddenly can trigger anxiety, tremor, a racing heart and, in severe cases, seizures. Psychologically, alcohol becomes the default answer to stress, low mood and sleeplessness, even though in the long run it tends to make anxiety and depression worse.',
					'Dependence rarely arrives overnight. It creeps up through years of seemingly controlled drinking: a habit of unwinding with a drink becomes a need, tolerance pushes the quantities up, and drinking moves from social occasions into secrecy. Long before any physical addiction develops, habitual drinking can quietly strain relationships, careers, finances and health, which is why the belief that alcoholism only counts once it is physical is so misleading.',
					'Acknowledging that drinking is no longer fully under control is the hardest step, and the only one you have to take alone. At The Diamond Rehab Thailand in Hua Hin, our doctors, psychologists and therapists build a residential program around your circumstances, from a medically supervised detox through therapy and aftercare. If any of this feels familiar, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'faqCptIds' => [ 218, 32, 181, 206, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		884 => [
			'slug' => 'heroin',
			'programTag' => 'heroin program',
			'hero' => [
				'eyebrow' => 'Heroin and opiate addiction treatment · Hua Hin',
				'headline' => 'Break free from heroin and opiates, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medically supervised detox, evidence-based therapy and a hard cap of twelve clients, so withdrawal is managed safely and recovery is built around you, never a template.",
				'stat3Label' => 'Years treating opioid addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Heroin dependence builds quickly and quietly. These are the patterns families notice first. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of heroin addiction',
				'card1Items' => [
					'Needing higher doses to feel the same effect',
					'Using to feel normal rather than to feel high',
					'Moving from prescription painkillers to stronger opiates',
					'Preoccupation with obtaining and using',
					'Withdrawal discomfort within hours of the last dose',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Cramps, vomiting and diarrhoea',
					'Cold sweats and fever',
					'Insomnia and disrupted sleep',
					'Anxiety and low mood',
					'Intense physical cravings',
				],
			],
			'holistic' => [
				'eyebrow' => 'A complex addiction',
				'heading' => 'A holistic approach to a powerful dependency',
				'body' => "Heroin creates one of the strongest physical dependencies of any drug, which is why willpower alone so rarely holds. The drug targets the brain's opioid receptors and, over time, the brain stops producing normal amounts of dopamine, so using becomes less about pleasure and more about feeling normal. Our program treats both sides of that equation: the physical dependence and the psychological issues underneath it.\n\nThe path runs from medication-assisted detox under round-the-clock medical care, through daily therapy with Western-trained counsellors, into mindfulness, exercise and the habits that protect sobriety after you leave. All of it happens in a calm, private setting in Hua Hin, with a clinical team focused entirely on a small number of clients.",
			],
			'phases' => [
				'heading' => 'Three pillars of heroin recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised start',
						'paragraphs' => [
							'For most clients, heroin recovery begins with detox.',
							'Withdrawal starts within hours of the last dose: cramps, vomiting, cold sweats, fever, insomnia and anxiety. It is rarely life-threatening, but it is intense enough that many people attempting it alone give up. Our medical team provides 24-hour care and, where appropriate, medication to ease the discomfort while your body adjusts.',
						],
						'listItems' => [
							'Round-the-clock care from licensed medical staff',
							'Medication to ease withdrawal where appropriate',
							'Psychological support through the hardest days',
						],
						'asideQuote' => '"Heroin withdrawal is rarely dangerous under medical care, but it is the point where most people give up alone. We make sure nobody has to."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Treating the root cause',
						'paragraphs' => [
							'Detox breaks the physical dependence. Therapy addresses why it formed.',
							"You'll work one-on-one with Western-trained counsellors experienced in opioid addiction, three sessions a week alongside daily lectures and neuroscience education. Together you'll confront the emotional and psychological factors behind the addiction, map your triggers and destructive patterns, and build the honesty, communication and mindfulness that recovery depends on.",
						],
						'listItems' => [
							'Three one-to-one counselling sessions a week',
							'Daily lectures and neuroscience education',
							'Practical tools for triggers and cravings',
						],
						'asideQuote' => '"Detox gets heroin out of the body. Therapy is where we work out how it got in, and how to keep it out."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built to last after you leave',
						'paragraphs' => [
							'Lasting sobriety rests on overall health, not just abstinence.',
							'Mindfulness practice, yoga and structured exercise rebuild the body and the daily routine that heroin dismantled. Before you leave, we put an aftercare plan in place: weekly online sessions with the counsellor who treated you, or an introduction to one of our aftercare partners near your home, wherever in the world that is.',
						],
						'listItems' => [
							'Mindfulness, yoga and structured exercise',
							'Weekly online sessions with your counsellor after discharge',
							'A worldwide network of aftercare partners',
						],
						'asideQuote' => '"Leaving treatment is not the end of recovery. The habits and support built here are what carry it home."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Outpatient treatment leaves you inside the same environment, routines and relationships that fed the addiction, and with heroin, that makes early relapse far too easy. Residential treatment in Thailand removes those triggers entirely, keeps medical help close day and night, and gives recovery the structure it needs to hold.',
			],
			'prose' => [
				'heading' => 'Is it time to consider heroin rehab?',
				'paragraphs' => [
					'Heroin is a fast-acting opioid derived from the opium poppy, and dependence on it is a medical condition, not a failure of character. Whether injected, smoked or snorted, it produces an intense feeling of well-being, and because it acts much faster than other opioids, dependence can take hold quickly. Many people are surprised by how soon use stops being a choice.',
					"The drug works on the brain's natural opioid receptors, the same system that regulates pain and reward. With each exposure the effect diminishes, so higher doses are needed for the same result. Over time the brain stops producing normal amounts of dopamine on its own, and using shifts from chasing a high to simply trying to feel normal.",
					'For many people, the path starts with prescription opioid painkillers, which carry a high addiction potential and act on the brain in much the same way. When a prescription runs out, heroin can appear as a more potent and affordable substitute. And because heroin produces stronger physical withdrawal than most other drugs, stopping without support is something few manage alone.',
					"Deciding to give up heroin is hard, and committing to treatment can feel harder still. It doesn't have to be decided all at once. If several of the signs above feel familiar, for you or for someone you love, a confidential conversation with our team is a small, pressure-free first step.",
				],
			],
			'faqCptIds' => [ 173, 183, 198, 214, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		902 => [
			'slug' => 'crack',
			'programTag' => 'crack program',
			'hero' => [
				'eyebrow' => 'Crack addiction treatment · Hua Hin',
				'headline' => 'Break free from crack, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Supervised detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating stimulant addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Crack dependence moves quickly and rarely stays hidden. These are the patterns families notice first. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of crack addiction',
				'card1Items' => [
					'Crack has become a major priority in daily life',
					'Mood, finances, career or relationships are suffering',
					'Health problems linked directly or indirectly to use',
					'Rising tolerance, taking larger doses to get the same high',
					'Failed attempts to quit without support',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Intense cravings and preoccupation with the drug',
					'Paranoia, body aches and intense sweating in the first days',
					'Nausea, vomiting and, in some cases, hallucinations',
					'Depression, anxiety and uncontrollable mood swings',
					'Fatigue, irritability and apathy in the following weeks',
				],
			],
			'holistic' => [
				'eyebrow' => 'A complex addiction',
				'heading' => 'A holistic approach to a relentless dependency',
				'body' => "Crack delivers a rush of dopamine and a euphoria that lasts only minutes, followed by a crash that drives the next dose. Repeated use alters the chemistry of the brain, which is why willpower alone so rarely holds. Our program treats the whole addiction: the physical dependence, and the emotional and psychological patterns underneath it.\n\nThe path runs from supervised medical detox into daily one-on-one therapy with qualified addiction counsellors, blending evidence-based Western techniques with Eastern practice, alongside holistic work that rebuilds health and routine. All of it happens in a calm, private setting in Hua Hin, designed so you can focus entirely on recovery.",
			],
			'phases' => [
				'heading' => 'Three pillars of crack recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised start',
						'paragraphs' => [
							'Recovery starts with a body that is free of the drug.',
							'Crack withdrawal is often severe enough that people detoxing alone abandon the attempt and return to the drug. Our physicians design each detox around your usage, body chemistry and medical history, with round-the-clock care and medication where appropriate to ease the most difficult symptoms.',
						],
						'listItems' => [
							'First 72 hours: paranoia, body aches, nausea and sweating',
							'Weeks one to two: fatigue, low mood and strong cravings',
							'24/7 medical supervision from beginning to end',
						],
						'asideQuote' => '"Crack withdrawal is intense but short. With medical care around the clock, the hardest days are made safe and as comfortable as possible."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Rewiring the patterns behind use',
						'paragraphs' => [
							'Detox clears the drug. Therapy addresses why it was there.',
							'In one-on-one sessions, highly qualified addiction counsellors help you recognise and reprogramme the negative patterns that drive use, working through the emotional and environmental triggers behind it. Treating crack addiction at this level is what makes recovery hold for the long term.',
						],
						'listItems' => [
							'One-on-one sessions with qualified addiction counsellors',
							'Evidence-based Eastern and Western therapeutic techniques',
							'Work on the emotional and environmental triggers of use',
						],
						'asideQuote' => '"The drug is the symptom. Therapy works on the patterns and the pain underneath it, so the change holds after you leave."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'A plan that travels home with you',
						'paragraphs' => [
							'Long-term recovery is built before you go home.',
							'You will leave with a comprehensive relapse prevention plan and a set of healthy coping mechanisms for managing temptation in the years ahead. If a relapse does happen, it is not failure: it is a stumbling block to learn from, and you will always have the full support of The Diamond Rehab Thailand.',
						],
						'listItems' => [
							'A personal relapse prevention plan',
							'Healthy coping mechanisms for temptation and stress',
							'Ongoing support from The Diamond team after discharge',
						],
						'asideQuote' => '"A relapse is not failure. It is information. Every client leaves with a plan for the years ahead, not just the first month."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Crack withdrawal is often severe enough that people who try to quit at home return to the drug within days. Residential treatment removes you from the social circles, stressors and destructive influences that fuel use, and places you in a controlled environment with no access to drugs, so recovery actually has room to take hold.',
			],
			'prose' => [
				'heading' => 'Is it time to consider crack rehab?',
				'paragraphs' => [
					'Crack is a powerful, highly addictive stimulant derived from powdered cocaine and processed into a solid, smokable form. It is typically smoked, though it can also be snorted or injected. Smoking sends the drug to the brain almost instantly, which makes the high faster, more intense and far shorter than powder cocaine. Use peaked in the late 1980s, but crack remains a commonly abused substance in communities across the globe.',
					"The addictive pull comes from what crack does to the brain. Each dose triggers a rush of dopamine, the neurochemical at the centre of the body's pleasure and reward systems, producing an intense euphoria that lasts only a few minutes. Unlike sedating drugs, crack heightens alertness and excitement, and when the high collapses into a debilitating crash, the fastest way to feel normal again appears to be another dose.",
					'That cycle escalates. Repeated use alters the chemistry of the brain, and as tolerance builds, users take increasingly larger doses to replicate the same effect. What begins as occasional use hardens into both a physical and a psychological dependence, often with a profound impact on mental health, mood and judgement along the way.',
					'That same impact on clear thinking is what makes the problem hard to admit. If crack has become a priority in your life, if your health, finances or relationships are suffering, or if you have tried to stop and could not stay off it, those are reasons enough to talk to someone. It is never too early or too late to seek help, and a confidential conversation with our team in Hua Hin is a quiet, pressure-free place to start.',
				],
			],
			'faqCptIds' => [ 218, 178, 182, 198, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		974 => [
			'slug' => 'marijuana',
			'programTag' => 'marijuana program',
			'hero' => [
				'eyebrow' => 'Marijuana addiction treatment · Hua Hin',
				'headline' => 'Break free from marijuana, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Supervised stabilisation, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating cannabis addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Because marijuana has a reputation as a harmless drug, dependence is easy to miss, even in yourself. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of marijuana addiction',
				'card1Items' => [
					'Losing interest in hobbies you used to enjoy',
					'Withdrawing from friends and family',
					'Neglecting family, work or study obligations because of use',
					'Feeling unable to stop, even after negative consequences',
					'Needing increasingly larger amounts to feel the same effect',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Insomnia and other sleep difficulties',
					'Irritability and mood swings',
					'Strong cravings for marijuana',
					'Headaches and restlessness',
					'Difficulty concentrating and low mood',
				],
			],
			'holistic' => [
				'eyebrow' => 'Treating the whole person',
				'heading' => 'A holistic approach to marijuana addiction',
				'body' => "Marijuana dependence is primarily psychological, which is exactly why willpower alone so often fails. With repeated use the brain adapts, tolerance builds, and the drug quietly becomes the way you manage stress, sleep and mood. Many people also use cannabis to self-medicate depression or anxiety, so stopping means facing what the smoking was covering. Our program treats those roots, not just the habit.\n\nYour stay begins with a thorough psychiatric and medical assessment, then moves into daily one-to-one and group therapy built around cognitive behavioural therapy and dialectical behaviour therapy. Eastern wellness practices, yoga, mindfulness meditation and exercise therapy reconnect mind and body, all within a calm, private residence in Hua Hin where there is nothing to manage except getting well.",
			],
			'phases' => [
				'heading' => 'Three pillars of marijuana recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'Stabilise and rest',
						'paragraphs' => [
							'Cannabis withdrawal is mild compared to most drugs, but it is real and it is uncomfortable.',
							'In the first days our medical team focuses on restoring sleep, settling irritability and easing cravings under round-the-clock supervision. There is no dangerous physical withdrawal to fear, only a structured, comfortable reset while the drug leaves your system.',
						],
						'listItems' => [
							'Full medical and psychiatric assessment on arrival',
							'Sleep restoration and comfort-focused care',
							'24/7 nursing support through cravings and restlessness',
						],
						'asideQuote' => '"Cannabis withdrawal rarely needs heavy medical intervention, but it does need structure. We restore sleep first, because everything else in recovery builds on it."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Retrain the patterns',
						'paragraphs' => [
							'This is the heart of marijuana treatment, because the dependence lives in habits and thinking, not in the body.',
							'Daily cognitive behavioural therapy and dialectical behaviour therapy sessions explore the root issues driving use. Marijuana use overlaps strongly with depression, anxiety and psychosis, so our integrated model treats co-occurring conditions at the same time rather than leaving them to undermine recovery.',
						],
						'listItems' => [
							'One-to-one CBT and DBT with experienced counsellors',
							'Integrated treatment for co-occurring mental health conditions',
							'Practical coping skills for stress, sleep and social pressure',
						],
						'asideQuote' => '"Most clients tell us the weed was never really the problem. It was the way they coped with everything else. Therapy is where that changes."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Rebuild and return',
						'paragraphs' => [
							'A full, structured day is one of the strongest protections against relapse.',
							'Yoga, mindfulness meditation, exercise therapy and spa treatments rebuild the mind-body connection while you practise life without cannabis. Before you leave, we build a tailor-made relapse prevention plan so the calm you find here travels home with you.',
						],
						'listItems' => [
							'Daily yoga, mindfulness and exercise therapy',
							'A busy, productive schedule that reduces cravings',
							'A personal relapse prevention plan and ongoing aftercare',
						],
						'asideQuote' => '"Recovery is not finished at discharge. Every client leaves with a plan, a routine and a team they can still call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Research shows residential treatment is the most effective way to overcome marijuana addiction. Staying with us removes you from the routines, social circles and triggers that keep the habit alive, and replaces them with a calm, private residence in Hua Hin, a team available around the clock, and a full schedule of therapy, holistic practice and recreation. Treatment is completely confidential from enquiry to discharge, and most clients stay a minimum of 28 days.',
			],
			'prose' => [
				'heading' => 'Is it time to consider marijuana rehab?',
				'paragraphs' => [
					'Marijuana, also called cannabis, weed or pot, is a psychoactive drug from the Cannabis sativa plant. Its active compound, THC, produces relaxation and altered perception, and with regular use it can lead to cannabis use disorder: a recognised condition where a person keeps using despite clear harm to their health, relationships or work. Studies indicate that around 30 percent of people who use marijuana may have some degree of marijuana use disorder.',
					'Dependence develops because the brain adapts to the steady presence of THC. Tolerance builds, larger or more frequent doses are needed to feel the same effect, and over time cannabis becomes tied to sleep, appetite and mood regulation. The risk is significantly higher for people who start young: those who begin before the age of 18 are about five times more likely to develop a use disorder than those who start as adults.',
					'Because marijuana is widely accepted and often seen as harmless, escalation tends to go unnoticed, including by people using it medicinally or to self-medicate anxiety, low mood or poor sleep. The pattern usually shows itself indirectly: hobbies fade, social circles narrow to other users, obligations slip, and attempts to cut back end in irritability, restless nights and a return to smoking.',
					'If any of this sounds familiar, that recognition is the most important step, and it deserves a considered response rather than another attempt to quit alone. Our team can talk you through how a private, doctor-led residential program in Hua Hin works, what a typical stay looks like, and whether it is the right fit for you. The conversation is confidential and there is no obligation.',
				],
			],
			'faqCptIds' => [ 3451, 3436, 3433, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		964 => [
			'slug' => 'ketamine',
			'programTag' => 'ketamine program',
			'hero' => [
				'eyebrow' => 'Ketamine addiction treatment · Hua Hin',
				'headline' => 'Break free from ketamine, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medical detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating ketamine addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Ketamine is a dissociative anaesthetic, and it can quietly warp your sense of what is normal. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of ketamine addiction',
				'card1Items' => [
					'Mood swings and sudden changes in personality',
					'Loss of mental clarity and impaired memory',
					'Painful urination or bladder problems',
					'Engaging in risky behaviour while dissociated',
					'Neglecting work, family or study because of ketamine',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Intense ketamine cravings',
					'Insomnia and nightmares',
					'Depression and paranoia',
					'Tremors and an increased heart rate',
					'Hallucinations or delirium',
				],
			],
			'holistic' => [
				'eyebrow' => 'Whole-person care',
				'heading' => 'A holistic approach to ketamine recovery',
				'body' => "Ketamine dependence rarely responds to willpower alone. The drug's dissociative pull, rising tolerance and the psychological patterns underneath it all need to be treated together, not one at a time. Our program looks past the substance to the person: the stress, the habits and the triggers that keep the cycle turning.\n\nYour path runs from a medically supervised detox through evidence-based psychotherapy and into holistic work, mindfulness, yoga and exercise therapy, all in a calm, private setting in the hills of Hua Hin. With no more than twelve clients in residence, every part of your treatment is shaped around you.",
			],
			'phases' => [
				'heading' => 'Three pillars of ketamine recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, monitored reset',
						'paragraphs' => [
							'Recovery starts with clearing ketamine from your system, safely.',
							'Ketamine withdrawal is mostly psychological, but it can be severe: cravings, insomnia, paranoia and low mood are common. Our clinical team monitors you around the clock in a controlled, comfortable environment until your mind and body have settled.',
						],
						'listItems' => [
							'Comprehensive medical evaluation on arrival',
							'24-hour nursing and medical support',
							'Continuous monitoring for comfort and safety',
						],
						'asideQuote' => '"Upon your arrival you\'ll receive a comprehensive medical evaluation. Based on the results, our clinical team may recommend starting in our medically supervised detox, with 24-hour care as the drug exits your body."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Understand the why',
						'paragraphs' => [
							'Detox clears the drug. Therapy addresses what kept you reaching for it.',
							"Working one-to-one with Western-trained counsellors, you'll use cognitive behavioural therapy and our 12-step program to explore the inner mechanisms of ketamine addiction, challenge destructive thought patterns and build practical problem-solving skills.",
						],
						'listItems' => [
							'Cognitive behavioural therapy (CBT)',
							'A proven 12-step program',
							'Tools to challenge destructive thinking',
						],
						'asideQuote' => '"Lasting recovery means breaking free from the psychological and behavioural patterns that drive drug use, not just stopping the drug itself."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Skills that travel home',
						'paragraphs' => [
							'Sustainable recovery is built before you leave, not after.',
							"Alongside therapy, you'll restore body and mind with mindfulness meditation, yoga and exercise therapy. You graduate with a comprehensive relapse prevention plan and skills you can carry into daily life long after Hua Hin.",
						],
						'listItems' => [
							'Mindfulness, yoga and exercise therapy',
							'A comprehensive relapse prevention plan',
							'Structured aftercare once you return home',
						],
						'asideQuote' => '"Staying clean in rehab is not the hard part. Our job is to send you home with the tools, and the aftercare, to stay well in the real world."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Our residential program removes the triggers and stressors that fuel ketamine use. In a private villa in the hills of tropical Hua Hin, with ocean views, a personal pool and wholesome dining, you are worlds away from the environments where the drug lived, free to give the recovery process your full attention.',
			],
			'prose' => [
				'heading' => 'Is it time to consider ketamine rehab?',
				'paragraphs' => [
					'Ketamine, sometimes sold as K, Special K or Ket, is a dissociative anaesthetic first developed in 1962 and still used legitimately in medicine. In a clinical setting it is generally regarded as safe. Used recreationally, usually as a fine white powder that is snorted, swallowed or injected, it is a different story: regular use can slide into dependence faster than most people expect.',
					'What makes ketamine so habit-forming is the escape it offers. As a powerful psychotropic, it alters how you perceive reality, and that dissociation can become a refuge from stress, anxiety or low mood. The effects last only around an hour, and as tolerance builds, users feel compelled to take larger and more frequent doses to reach the same place, deepening the psychological grip of the drug.',
					'Escalating use carries a real physical toll. Chronic ketamine use is linked to memory impairment, bladder damage, painful urination, respiratory issues, elevated blood pressure, seizures and psychosis. High doses can cause severe confusion, loss of consciousness and, in rare cases, fatal overdose, a risk that rises sharply when ketamine is mixed with alcohol or other drugs.',
					'There is no right or wrong moment to seek help, and recognising a problem is harder with a drug that warps your sense of reality. If ketamine has started costing you your memory, your health or your relationships, that is reason enough. Our admissions team can talk you through what a stay at The Diamond would look like, confidentially and without obligation.',
				],
			],
			'faqCptIds' => [ 218, 181, 32, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		946 => [
			'slug' => 'mdma',
			'programTag' => 'ecstasy program',
			'hero' => [
				'eyebrow' => 'Ecstasy addiction treatment · Hua Hin',
				'headline' => 'Step off the ecstasy cycle, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Supported detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating party drug addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Many people still believe MDMA can't lead to dependency, which keeps them from getting help. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of ecstasy addiction',
				'card1Items' => [
					'Using MDMA in risky or dangerous situations',
					'Needing more of the drug to feel the same effect',
					'Wanting to cut down but not managing to',
					'Neglected responsibilities, strained relationships and work',
					'Long stretches spent finding, using or recovering from the drug',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Depression and low mood as serotonin levels recover',
					'Fatigue, insomnia and disturbed sleep',
					'Anxiety, irritability and panic',
					'Trouble with memory and concentration',
					'Strong cravings and loss of appetite',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to ecstasy recovery',
				'body' => "People become dependent on MDMA for all sorts of reasons, so there is no one-size-fits-all treatment. The drug floods the brain with serotonin, dopamine and norepinephrine, and the crash that follows is longer than with most other stimulants. Willpower alone rarely carries someone through it, which is why our program treats the whole person: the comedown, the cravings and the anxiety, low mood or stress that ecstasy was masking.\n\nYour path runs from a supported detox through private counselling, group sessions and behavioural therapy, alongside exercise, nutrition and mindfulness practice. All of it happens in a calm, private setting by the sea in Hua Hin, where the focus stays on getting well rather than getting through.",
			],
			'phases' => [
				'heading' => 'Three pillars of ecstasy recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supported comedown',
						'paragraphs' => [
							'Quitting ecstasy is no easy feat for a long-term user.',
							"MDMA disrupts the brain's serotonin, dopamine and norepinephrine systems, and the crash can feel unbearable without support. Our medical team monitors your mood, sleep and physical health around the clock, easing symptoms so the first days of recovery feel manageable rather than punishing.",
						],
						'listItems' => [
							'24-hour medical and psychiatric oversight',
							'Symptom relief for sleep, anxiety and low mood',
							'Screening for other substances used alongside MDMA',
						],
						'asideQuote' => '"The crash after stopping ecstasy is longer than most people expect. Nobody should have to sit through it alone."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the root of it',
						'paragraphs' => [
							'Detox clears the drug. Therapy is what keeps it gone.',
							'Working one to one with qualified psychotherapists and counsellors, and in small group sessions, you examine what ecstasy was doing for you: the stress, the social pressure, the low moods it papered over. Behavioural therapy then builds the practical skills to handle those triggers without the drug.',
						],
						'listItems' => [
							'Private counselling and small group therapy',
							'Behavioural therapy targeting cravings and triggers',
							'Treatment for anxiety, depression and co-occurring issues',
						],
						'asideQuote' => '"Relapse prevention is just as crucial as detox. We treat the reasons behind the use, not only the use itself."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => "A life that doesn't need the high",
						'paragraphs' => [
							'Recovery has to outlast your stay with us.',
							'Exercise, dietary guidance and mindfulness meditation help restore the energy and mood that ecstasy depleted, while spa, sauna and time by the coast rebuild the habit of feeling good naturally. Before you leave, we map out a relapse prevention plan and ongoing support for the months ahead.',
						],
						'listItems' => [
							'Exercise, nutrition and mindfulness practice',
							'A personal relapse prevention plan',
							'Continued support after you return home',
						],
						'asideQuote' => '"Our job is to send you home with more than sobriety: a routine, a plan and people to call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Our residential program brings detox, therapy and recovery under one roof on the Hua Hin coast. With no more than twelve clients at a time, your MDMA treatment plan is genuinely bespoke: medical oversight through the comedown, daily one-to-one and group therapy, and the space to rest, train and eat well while your mind and body recover. You are a guest in a private retreat, never a patient in a facility.',
			],
			'prose' => [
				'heading' => 'Is it time to consider ecstasy rehab?',
				'paragraphs' => [
					'MDMA, short for 3,4-methylenedioxymethamphetamine and better known as ecstasy or molly, is a synthetic stimulant with hallucinogenic properties. It is most familiar as a party drug in clubs and at festivals, popular with young adults, but people from every demographic can become dependent on it. Pills and powders sold as ecstasy are also frequently cut with other substances, which makes every dose an unknown quantity.',
					'A persistent myth holds that MDMA is not addictive. In reality, the drug works by forcing the brain to release a surge of serotonin, dopamine and norepinephrine, and the days of depleted mood that follow leave many users reaching for the next dose to feel normal again. Tolerance builds, doses creep up, and the gap between highs gets harder to sit through. The crash after ecstasy is also notably long compared with other stimulants.',
					'As use escalates, the costs mount. Long-term ecstasy abuse is linked to anxiety, depression, paranoia, panic attacks and problems with memory and concentration, and in severe cases delusions or psychosis. Many users also drink heavily or combine MDMA with other drugs such as ketamine, compounding the risks. Responsibilities slip, relationships strain, and more and more time disappears into finding the drug or recovering from it.',
					'Acknowledging that ecstasy has become a problem is the hardest step, and the most important one. Treatment that combines a supported detox with behavioural therapy and relapse prevention gives you the best chance of a lasting recovery. If you recognise yourself or someone you love in any of this, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'faqCptIds' => [ 218, 173, 32, 198, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		952 => [
			'slug' => 'ghb',
			'programTag' => 'GHB program',
			'hero' => [
				'eyebrow' => 'GHB addiction treatment · Hua Hin',
				'headline' => 'Break free from GHB, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medically supervised detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating GHB addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "GHB dependence can build fast, often faster than people expect from a party drug. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of GHB addiction',
				'card1Items' => [
					'Strong cravings, sometimes dosing around the clock',
					'Relying on GHB to socialise or feel confident',
					'Taking larger or more frequent doses as tolerance builds',
					'Trying to quit but failing, or using simply to hold off withdrawal',
					'Relationships, work or education starting to suffer',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Severe anxiety and restlessness',
					'Insomnia that resists ordinary remedies',
					'Tremors, sweating and a racing heart',
					'Confusion, hallucinations or delirium',
					'In severe cases, seizures, which is why supervision matters',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to GHB recovery',
				'body' => "Willpower alone rarely beats GHB, because the addiction is never only chemical. Rapid tolerance, round-the-clock dosing, disrupted sleep and the social settings the drug lives in all pull in the same direction, so our program treats the whole person: the physical dependence, the thought patterns beneath it and the lifestyle around it.\n\nThe path runs from a medically supervised detox through evidence-based therapy, including cognitive behavioural therapy and our 12-step program, into holistic work such as mindfulness, yoga and exercise therapy. All of it unfolds in a calm, private resort-style setting in Hua Hin, with a team that shapes treatment around your own goals rather than a fixed plan.",
			],
			'phases' => [
				'heading' => 'Three pillars of GHB recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised landing',
						'paragraphs' => [
							'Detox is where GHB recovery begins, and it is the one stage that should never be attempted alone.',
							'Because GHB is metabolised quickly, withdrawal can start within hours of the last dose and may last up to two weeks. Our clinical team manages a carefully tapered, medically supervised detox with round-the-clock care, easing symptoms and stepping in immediately if confusion or agitation escalates.',
						],
						'listItems' => [
							'24/7 medical monitoring',
							'Carefully tapered withdrawal',
							'Comfort-focused symptom relief',
						],
						'asideQuote' => '"GHB withdrawal is one of the few we treat as a medical event in its own right. Nobody should face it without clinical support close at hand."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Understanding the why',
						'paragraphs' => [
							'Once the body is stable, the real work begins.',
							'Working closely with our experienced addiction therapists, you will explore the emotional, behavioural and mental patterns that underpin GHB use, then build practical strategies for handling stress, social pressure and cravings in a healthy, sustainable way.',
						],
						'listItems' => [
							'Cognitive behavioural therapy',
							'One-to-one and group sessions',
							'12-step and relapse-prevention work',
						],
						'asideQuote' => '"Detox clears the drug. Therapy is where clients learn why they reached for it, and what to reach for instead."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Habits that hold',
						'paragraphs' => [
							'Recovery is a marathon, not a sprint, and there are no quick fixes.',
							'Mindfulness meditation, yoga and exercise therapy rebuild the sleep, energy and calm that GHB use erodes, while a structured aftercare plan keeps support in place long after you leave Hua Hin, lowering the risk of relapse in the years ahead.',
						],
						'listItems' => [
							'Mindfulness and yoga',
							'Exercise and sleep restoration',
							'Structured aftercare planning',
						],
						'asideQuote' => '"The habits built here are designed to outlast the program. That is what long-term wellness means to us."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Outpatient care can seem convenient, but it leaves you inside the same social circles and stressors that fuel GHB use, and for a drug with withdrawal this unpredictable, that is a genuine risk. Our residential program in Hua Hin removes those triggers entirely: a private villa, pool and spa set against the ocean, a fully qualified clinical and therapeutic team on site, and a calm, structured environment where all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider GHB rehab?',
				'paragraphs' => [
					'Gamma-hydroxybutyrate, better known as GHB, liquid ecstasy or fishies, is a central nervous system depressant first used as an anaesthetic in the 1960s. Today it circulates mainly as a recreational party drug, and among bodybuilders for its effect on growth hormone. Usually a clear liquid or white powder, it takes effect within about fifteen minutes and lasts three to six hours, producing an alcohol-like euphoria, confidence and heightened sex drive.',
					"That easy euphoria comes from the way GHB acts on the brain's GABA system, slowing the central nervous system much as alcohol does. Tolerance builds quickly, so doses become larger and more frequent, and because the drug is metabolised rapidly, the effects fade within hours. Heavy users often end up dosing around the clock simply to keep withdrawal at bay, which is how dependence can take hold far faster than most people expect.",
					'Escalation carries two serious dangers. GHB is usually an unmarked liquid that is almost impossible to dose accurately, so overdose, particularly when it is mixed with alcohol or other drugs, can slow breathing, heart rate and blood pressure to dangerous levels. And once dependence is established, stopping abruptly is genuinely unsafe: withdrawal can begin within hours of the last dose, last up to two weeks, and bring insomnia, severe anxiety, tremor, a racing heart and, in heavier users, confusion, hallucinations and delirium. This is why GHB detox should always be medically supervised.',
					'Because GHB is usually taken socially, many people are slow to recognise that use has become a problem. If you find yourself craving it between sessions, relying on it to socialise, or trying to stop and not managing to, those are signals worth taking seriously. A confidential conversation with our team in Hua Hin is a small step, and it may be the one that changes everything.',
				],
			],
			'faqCptIds' => [ 218, 215, 32, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is GHB withdrawal dangerous?',
					'answer' => 'Yes, it can be. Because GHB is metabolised quickly, withdrawal can begin within hours of the last dose and may last up to two weeks, bringing insomnia, severe anxiety, tremor, a racing heart and, in heavy users, confusion, hallucinations and delirium. These symptoms can escalate unpredictably, so GHB detox should never be attempted alone at home. At The Diamond Rehab Thailand, withdrawal is managed by a medical team with round-the-clock care to keep you safe and as comfortable as possible.',
				],
			],
		],
		1011 => [
			'slug' => 'xanax',
			'programTag' => 'Xanax program',
			'hero' => [
				'eyebrow' => 'Xanax addiction treatment · Hua Hin',
				'headline' => 'Step off Xanax safely, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. A slow, medically supervised taper, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating benzodiazepine addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Xanax dependence is easy to miss, especially when it began with a prescription. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of Xanax addiction',
				'card1Items' => [
					'Drowsiness, sluggishness and excessive sleeping',
					'Slurred speech and impaired coordination',
					'Cognitive impairment, trouble with memory and focus',
					'Nausea and vomiting',
					'Withdrawing from friends and spending more time in isolation',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Rebound anxiety and panic attacks',
					'Insomnia and restless sleep',
					'Muscle spasms, aches and headaches',
					'Nausea and, in some cases, delirium',
					'In severe cases, hallucinations, heart palpitations or seizures, which is why stopping abruptly is never safe',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to Xanax addiction',
				'body' => "Xanax dependence is rarely a question of willpower. For many of our clients it began as a legitimate prescription for anxiety or panic, and the drug did its job, until tolerance crept in and the body stopped knowing how to feel calm without it. Because Xanax changes how the central nervous system works, simply deciding to stop is not only ineffective, it can be dangerous.\n\nThe way through is a slow, medically supervised taper, then honest therapeutic work on the anxiety that started it all. At The Diamond, detox, one-to-one psychotherapy, group sessions, counselling and practices like meditation are woven into a single tailored program, delivered in a calm, private setting by the sea in Hua Hin, so you can rebuild a life that no longer needs the prescription.",
			],
			'phases' => [
				'heading' => 'Three pillars of Xanax recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A slow, safe taper',
						'paragraphs' => [
							'With benzodiazepines, stopping abruptly is not just uncomfortable. It can be dangerous.',
							'Our medical team manages a gradual, carefully monitored taper in our purpose-built facility, easing rebound anxiety, insomnia and muscle spasms around the clock so your nervous system can recalibrate without ever going into freefall.',
						],
						'listItems' => [
							'Doctor-led tapering plan, never cold turkey',
							'24-hour medical monitoring and care',
							'Comfort-focused relief for withdrawal symptoms',
						],
						'asideQuote' => '"We never rush a benzodiazepine detox. A careful, supervised taper is the difference between a safe beginning and a dangerous one."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Treating the anxiety underneath',
						'paragraphs' => [
							'Xanax almost always starts as an answer to anxiety. Recovery means finding better ones.',
							'Through one-to-one psychotherapy, group sessions and counselling, our psychologists and behavioural therapists work with you on the panic and worry that made the prescription feel necessary, and teach you healthy ways to manage anxiety so you never feel the need to return to the drug.',
						],
						'listItems' => [
							'One-to-one psychotherapy and CBT',
							'Group sessions and counselling',
							'Practical tools for managing anxiety and panic',
						],
						'asideQuote' => '"Until the anxiety underneath is treated, the pull back to Xanax remains. That is where the real work of recovery happens."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'A life that stays calm without it',
						'paragraphs' => [
							'Leaving Xanax behind is one thing. Staying off it is another.',
							'Meditation, fitness, nutrition and restorative routines rebuild the natural calm the drug once supplied, and before you leave Hua Hin we put a structured aftercare plan in place, with continued sessions and support, so the quiet you found here travels home with you.',
						],
						'listItems' => [
							'Meditation and mindfulness practice',
							'Fitness, nutrition and restorative sleep',
							'A structured aftercare and relapse-prevention plan',
						],
						'asideQuote' => '"Our focus is the long term. Real recovery means finding the true cause of the anxiety, then building a life that no longer needs the prescription."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Benzodiazepine recovery asks for time, stability and round-the-clock medical care, which is exactly what a residential stay provides. At our beachside estate in Hua Hin you step away from the prescriptions, pressures and routines that kept the cycle going, while our doctors manage your taper and our therapists work with you daily. With no more than twelve clients in residence, your program is genuinely built around you: private accommodation, discreet care and the space to recover at the pace your nervous system needs.',
			],
			'prose' => [
				'heading' => 'Is it time to consider Xanax rehab?',
				'paragraphs' => [
					'Xanax is the brand name for alprazolam, a benzodiazepine that doctors commonly prescribe to treat anxiety and panic disorders. It works quickly and its calming effect is real, which is precisely why it has become one of the most frequently prescribed, and most frequently misused, medications in the world.',
					'Xanax is addictive because of how it acts on the brain. It enhances the effect of GABA, the neurotransmitter that quiets the central nervous system, and with regular use the brain adapts and produces less calm of its own. Tolerance builds, the same dose does less, and the body gradually stops knowing how to function without the drug. Importantly, this can happen within prescribed use: many people who become dependent on Xanax never set out to misuse anything.',
					'As tolerance grows, doses creep up and the drug shifts from easing anxiety to causing it, with drowsiness, memory problems, slurred speech and withdrawal from the people and routines that used to matter. The greater danger comes from stopping suddenly. Withdrawal can begin within hours of the last dose, and going cold turkey can trigger severe symptoms including hallucinations, heart palpitations, psychosis and seizures. Xanax should never be stopped abruptly without medical supervision.',
					'If you are taking more than prescribed, dreading the gap between doses or have tried to stop and felt the withdrawal pull you back, those are signs worth taking seriously. A confidential conversation with our team costs nothing and commits you to nothing, and it is often the moment recovery quietly begins.',
				],
			],
			'faqCptIds' => [ 218, 32, 194, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is Xanax withdrawal dangerous?',
					'answer' => 'It can be. Because Xanax changes how the central nervous system works, stopping abruptly can send the body into overdrive. Withdrawal symptoms can begin within hours of the last dose, and while most are uncomfortable rather than harmful, such as rebound anxiety, insomnia and muscle spasms, severe cases can involve hallucinations, heart palpitations, psychosis and seizures. This is why we never recommend quitting cold turkey. A gradual, medically supervised taper, with doctors monitoring you throughout, is the safest and most comfortable way to come off Xanax.',
				],
			],
		],
		1036 => [
			'slug' => 'valium',
			'programTag' => 'Valium program',
			'hero' => [
				'eyebrow' => 'Valium addiction treatment · Hua Hin',
				'headline' => 'Step off Valium safely, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. A slow, medically supervised taper, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating benzodiazepine addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Valium dependence often builds quietly, even on a legitimate prescription. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of Valium addiction',
				'card1Items' => [
					'Taking Valium without a prescription, at higher doses or more often than prescribed',
					'Strong cravings for the next dose',
					'Continuing to use despite harm to health, career or relationships',
					'Neglecting obligations in order to use Valium',
					'Using Valium to enhance or counteract another substance',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Rebound anxiety and insomnia',
					'Abdominal cramps and muscle pain',
					'Sweating and numbness',
					'Confusion',
					'In severe cases seizures, which is why stopping abruptly is never safe',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to Valium recovery',
				'body' => "Willpower alone rarely beats a benzodiazepine, because the dependence is physical as much as mental. Valium works on the brain's GABA system, and with continued use the body comes to rely on it simply to feel calm. Many of our clients never set out to misuse anything: the dependence grew quietly out of a prescription for anxiety, sleep or muscle pain, and stopping abruptly is not just hard, it is unsafe.\n\nThe way through is a slow, medically supervised taper with 24/7 nursing care, daily therapy to unravel the issues the medication was quieting, and holistic work, from mindfulness meditation to exercise therapy, that teaches the body to settle on its own again. All of it happens in a calm, private retreat in Hua Hin, capped at twelve clients so the program is shaped entirely around you.",
			],
			'phases' => [
				'heading' => 'Three pillars of Valium recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A slow, supervised taper',
						'paragraphs' => [
							'Stopping Valium abruptly is dangerous, so we never ask you to.',
							'Because diazepam is long-acting, withdrawal can surface days after the last dose: rebound anxiety, insomnia, muscle pain, confusion and, in severe cases, seizures. Our doctors set a gradual, individually paced taper while qualified nurses provide round-the-clock support, with medication to ease discomfort where appropriate.',
						],
						'listItems' => [
							'Doctor-led tapering plan',
							'24/7 nursing support',
							'Medication to ease withdrawal',
						],
						'asideQuote' => '"Benzodiazepine withdrawal is one detox you should never attempt alone. A careful taper keeps it safe and bearable."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Treating what the pill was quieting',
						'paragraphs' => [
							'Valium almost always starts as a solution, for anxiety, sleep or pain.',
							'In one-to-one sessions, our Western-trained therapists help you unravel the issues the medication was covering and build practical ways to manage anxiety and cravings without it. Where a co-occurring condition is part of the picture, we treat both together in a single integrated plan.',
						],
						'listItems' => [
							'Evidence-based talk therapy',
							'Integrated dual-diagnosis care',
							'Practical relapse-prevention skills',
						],
						'asideQuote' => '"We look past the prescription to the person, and treat the reasons the medication became hard to put down."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Relearning calm without it',
						'paragraphs' => [
							'Recovery means teaching the body to rest, settle and sleep naturally again.',
							'Mindfulness meditation, exercise therapy, spa treatments and nourishing meals restore the balance Valium once imitated, while a structured aftercare plan keeps the support in place long after you fly home.',
						],
						'listItems' => [
							'Mindfulness and meditation',
							'Exercise and spa therapies',
							'Structured aftercare planning',
						],
						'asideQuote' => '"Leaving Hua Hin is not the end of treatment. We stay with you while the new routines take root."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Residential treatment is widely regarded as the most effective form of Valium addiction treatment, because a safe taper needs time, calm and constant clinical oversight. Stepping away from daily stressors and triggers into a private, structured environment lets you focus entirely on recovery, with staff available around the clock and a steady rhythm of therapy, wellness practices, nourishing meals and exercise that rebuilds routine from the first day.',
			],
			'prose' => [
				'heading' => 'Is it time to consider Valium rehab?',
				'paragraphs' => [
					'Diazepam, best known by the brand name Valium, is a long-acting benzodiazepine and central nervous system depressant. Doctors prescribe it for anxiety, muscle spasms, insomnia, seizures and alcohol withdrawal, and when taken exactly as prescribed for a short period it is generally considered safe. Because it is long-acting, it stays in the body longer and needs fewer doses than short-acting benzodiazepines.',
					'The risk lies in how it works. Valium enhances the effect of GABA, the neurotransmitter that calms hyperactive brain activity, and with continued use the brain adapts. Tolerance builds gradually, so the same dose does less over time, which is why doctors typically recommend taking diazepam for no longer than two to three months. Dependence can develop even in people taking it as prescribed, which makes it easy to miss until stopping becomes difficult.',
					'As tolerance grows, doses often creep upward, and the body becomes reliant on Valium to function. Long-term use can bring constant drowsiness, fatigue, depression, anxiety, and impaired concentration and memory. Stopping abruptly is the most dangerous response of all: withdrawal may not appear until days after the last dose, and in severe cases it can include seizures that are potentially fatal. Anyone who is physically dependent should only detox in a medically supervised setting, through a gradual taper.',
					'Acknowledging the problem is genuinely hard when the dependence began with a legitimate prescription. But if you are taking more than prescribed, craving the next dose or continuing despite the harm it is causing, those are signs worth acting on early. A confidential conversation with our clinical team can clarify where you stand and what a safe, comfortable path off Valium would look like for you.',
				],
			],
			'faqCptIds' => [ 32, 215, 218, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is Valium withdrawal dangerous?',
					'answer' => 'It can be. Symptoms range from abdominal cramps, muscle pain, anxiety, insomnia, sweating, numbness and confusion to, in severe cases, seizures that can be fatal. Because diazepam is long-acting, withdrawal may not appear until days after the last dose. Anyone who is physically dependent should never stop abruptly: a gradual, medically supervised taper with 24/7 nursing support keeps the process safe and as comfortable as possible.',
				],
			],
		],
		1022 => [
			'slug' => 'oxycodone',
			'programTag' => 'OxyContin program',
			'hero' => [
				'eyebrow' => 'OxyContin addiction treatment · Hua Hin',
				'headline' => 'Step away from OxyContin, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medically supervised opioid detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating opioid addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "OxyContin dependence often begins with a legitimate prescription for pain. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of OxyContin addiction',
				'card1Items' => [
					'Needing higher doses to get the same relief from pain',
					'Running out of prescriptions early, or seeking refills from more than one doctor',
					'Taking oxycodone for the calm or euphoria rather than the pain',
					'Feeling anxious or unwell when a dose is delayed or missed',
					'Continuing to use after the original pain has passed',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Anxiety and restlessness',
					'Insomnia',
					'Muscle aches and abdominal cramps',
					'Nausea and diarrhea',
					'Strong cravings for the next dose',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to OxyContin recovery',
				'body' => "OxyContin addiction is rarely a question of willpower. Oxycodone changes how the brain handles pain and reward, and tolerance builds quickly, even in people taking it exactly as prescribed. What starts as relief becomes a physical dependence the body defends fiercely, which is why stopping alone so often fails at the withdrawal stage.\n\nOur program addresses the whole picture. A medically supervised detox carries you safely through withdrawal, then individual and group therapy works on the thinking and stresses underneath the dependence, supported by fitness, mindfulness and proper rest. All of it happens in a calm, private setting in Hua Hin, with a team focused on no more than twelve clients at a time.",
			],
			'phases' => [
				'heading' => 'Three pillars of OxyContin recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'Withdrawal, managed safely',
						'paragraphs' => [
							'Oxycodone withdrawal can begin within six to 30 hours of the last dose, and it is physically intense.',
							'Our medically supervised detox keeps you safe and as comfortable as possible. Doctors and nurses provide round-the-clock care and appropriate medication while your body relearns how to function without oxycodone.',
						],
						'listItems' => [
							'24-hour medical supervision',
							'Medication to ease withdrawal symptoms',
							'Daily monitoring by our medical team',
						],
						'asideQuote' => '"Opioid withdrawal is one of the most common causes of relapse. Managed properly, it does not have to be."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Working on the why',
						'paragraphs' => [
							'Detox clears the drug. Therapy addresses what kept you reaching for it.',
							'Through individual and group sessions, including cognitive behavioural therapy and mindfulness, you learn to recognise and challenge the thought patterns behind the dependence and build practical skills for managing pain and stress without opioids.',
						],
						'listItems' => [
							'One-to-one CBT sessions',
							'Mindfulness-based therapy',
							'Small, focused group work',
						],
						'asideQuote' => '"Every treatment plan here is tailored to one person\'s needs, strengths and goals: yours."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'A plan that outlasts your stay',
						'paragraphs' => [
							'Recovery does not end when you leave Hua Hin.',
							'Alongside fitness, massage and time to properly rest, you graduate with a comprehensive relapse prevention plan, so the progress you make here carries into the months and years ahead.',
						],
						'listItems' => [
							'Personal relapse prevention plan',
							'Fitness, massage and mindfulness',
							'Continued support after you leave',
						],
						'asideQuote' => '"You leave with a plan for the years ahead, not just a goodbye."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Research shows inpatient care offers the strongest results for OxyContin addiction. Living on site removes you from the triggers, stressors and routines that keep opioid use going, while our team provides round-the-clock support and medical assistance through the most fragile early weeks of recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider OxyContin rehab?',
				'paragraphs' => [
					'Oxycodone is a powerful opioid pain reliever, prescribed for moderate to severe pain under brand names including OxyContin, Percocet and Endocet. OxyContin is the extended-release form, designed to release oxycodone into the body slowly over many hours.',
					"Oxycodone acts on the brain's opioid receptors, dulling pain and triggering a release of dopamine that can feel like deep calm or euphoria. The body adapts fast. Many people develop tolerance even while taking the medication exactly as prescribed, needing more of the drug to get the same relief and to keep withdrawal symptoms at bay.",
					'From there, use tends to escalate quietly: higher doses, prescriptions that run out early, requests to more than one doctor, and eventually tablets from other sources. Because oxycodone suppresses the respiratory system, this escalation is genuinely dangerous, and fatal overdoses are tragically common.',
					'Acknowledging that a medication has become a problem is hard, especially when it began with a legitimate prescription. If you recognise yourself or someone you love in this picture, our team in Hua Hin will talk it through with you, confidentially and without obligation.',
				],
			],
			'faqCptIds' => [ 218, 191, 32, 198, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		1053 => [
			'slug' => 'tramadol',
			'programTag' => 'tramadol program',
			'hero' => [
				'eyebrow' => 'Tramadol addiction treatment · Hua Hin',
				'headline' => 'Break free from tramadol, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. A carefully managed tramadol detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating opioid addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Tramadol dependence often begins with a legitimate prescription, which makes it easy to miss. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of tramadol addiction',
				'card1Items' => [
					'Taking tramadol beyond the prescribed dose or purpose',
					'Seeing multiple doctors to obtain more prescriptions',
					'Hiding pill bottles from friends and family',
					'Mood swings, drowsiness and trouble concentrating',
					'Continuing use despite problems at work or at home',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Sweating, chills and flu-like muscle pain',
					'Nausea, diarrhea and loss of appetite',
					'Anxiety, restlessness and a racing heart',
					'Insomnia, yawning and teary eyes',
					'In rare cases confusion or seizures, which is why detox is medically supervised',
				],
			],
			'holistic' => [
				'eyebrow' => 'Treatment philosophy',
				'heading' => 'A holistic approach to tramadol recovery',
				'body' => "Tramadol dependence rarely looks like a stereotype. It usually begins with a legitimate prescription for pain, a drug perceived as one of the milder opioids, and a dose that slowly stops being enough. Because tramadol acts on opioid receptors as well as serotonin and norepinephrine, the body adapts on several fronts at once, and willpower alone is rarely enough to undo that.\n\nOur approach restores balance to mind and body together: a carefully managed medical detox, daily psychotherapy to address what sits beneath the dependence, and wellness practices such as meditation, yoga and breathwork, all in a calm, private resort setting in Hua Hin where you can focus entirely on getting well.",
			],
			'phases' => [
				'heading' => 'Three pillars of tramadol recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised tramadol detox',
						'paragraphs' => [
							'Detox comes first, and with tramadol it is never done cold turkey.',
							'Stopping suddenly can trigger flu-like withdrawal alongside anxiety, confusion and, in rare cases, seizures. Our doctors typically taper the dose gradually, with 24-hour monitoring and medical support, so your body relearns how to function without the drug as safely and comfortably as possible.',
						],
						'listItems' => [
							'Doctor-led tapering plan',
							'24-hour medical monitoring',
							'Relief for withdrawal symptoms',
						],
						'asideQuote' => '"Your safety is our first priority. We watch every detox around the clock, so this delicate phase of recovery stays as safe and comfortable as it can be."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Understanding why you use',
						'paragraphs' => [
							'Detox clears the drug. Therapy addresses what kept you reaching for it.',
							"In daily one-to-one and group sessions you'll explore the thought patterns, behaviours and unresolved trauma that often sit beneath prescription drug dependence, then learn practical ways to recognise your triggers and respond differently.",
						],
						'listItems' => [
							'One-to-one psychotherapy',
							'Group sessions with a small community',
							'Practical tools for cravings and triggers',
						],
						'asideQuote' => '"Pills numb pain, they do not resolve it. Lasting recovery starts when you understand what the tramadol was doing for you."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Skills for life after rehab',
						'paragraphs' => [
							'Recovery is a long-term process, not a 28-day event.',
							"Alongside therapy you'll practise meditation, yoga and breathing exercises, skills that help you manage stress and pain without reaching for medication. You leave with a comprehensive aftercare plan to navigate triggers and prevent relapse in the years ahead.",
						],
						'listItems' => [
							'Meditation, yoga and breathwork',
							'Healthy routines for sleep and stress',
							'A personal aftercare and relapse-prevention plan',
						],
						'asideQuote' => '"You do not graduate from our care at the gate. Every client leaves with a plan for the years ahead, and with people to call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Leave your triggers behind. Our residential program in Hua Hin removes you from the routines, stressors and easy refills that keep tramadol use going, and replaces them with structure, 24-hour support and a small community of people working toward the same goal. With no more than twelve clients at a time, your treatment plan, your therapists and your days are genuinely built around you.',
			],
			'prose' => [
				'heading' => 'Is it time to consider tramadol rehab?',
				'paragraphs' => [
					'Tramadol is a synthetic opioid prescribed to relieve moderate to severe pain. It works by binding to opioid receptors in the brain and spinal cord, dampening the pain signals the body sends. Because it sits in a lighter regulatory category than most opioid painkillers, schedule IV in the United States, it is widely perceived as a mild and relatively safe option.',
					'That perception is exactly what makes tramadol risky. Beyond its opioid action it also affects serotonin and norepinephrine, producing the relaxation and mild euphoria the brain learns to seek out. Tolerance builds with regular use, larger doses are needed to feel the same relief, and physical dependence can develop even in people taking it exactly as prescribed.',
					'Once dependence has formed, stopping suddenly brings on withdrawal: sweating, muscle pain, chills, anxiety, insomnia, nausea and diarrhea, sometimes with confusion or, rarely, seizures. That is why doctors recommend tapering off tramadol gradually under medical supervision rather than quitting cold turkey. Escalating use carries its own dangers, because tramadol depresses the central nervous system and an overdose, marked by shallow breathing, a slow heart rate and seizures, can be life-threatening.',
					'If you are using tramadol outside its prescribed purpose, visiting several doctors for refills, or carrying on despite the toll it is taking on your life, those are signs worth acting on early. The sooner treatment begins, the easier recovery tends to be. Talk to our team in confidence and we will help you decide whether a residential program in Thailand is the right next step.',
				],
			],
			'faqCptIds' => [ 3435, 32, 206, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		1058 => [
			'slug' => 'ritalin',
			'programTag' => 'Ritalin program',
			'hero' => [
				'eyebrow' => 'Ritalin addiction treatment · Hua Hin',
				'headline' => 'Break the cycle of Ritalin, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Supervised detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating stimulant addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Ritalin dependence often hides behind a prescription or a demanding schedule. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of Ritalin addiction',
				'card1Items' => [
					'Taking higher or more frequent doses than prescribed',
					'Using Ritalin without a prescription to study or work longer',
					'Needing more of the drug to get the same focus or lift',
					'Anxiety, paranoia or loss of appetite during periods of heavy use',
					'A heavy crash and strong cravings whenever you try to cut back',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Deep fatigue and low energy',
					'Depression and flat mood',
					'Insomnia and vivid nightmares',
					'Increased appetite',
					'Persistent cravings',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to Ritalin recovery',
				'body' => "Ritalin dependence rarely looks like a stereotype. It often starts with a legitimate prescription, or with a student or professional borrowing a pill to push through a deadline, and quietly grows as tolerance builds and the doses climb. Because the drug is tied to performance, stopping can feel like giving up an edge, which is exactly why willpower alone so often fails: every demanding week becomes a reason to take it one more time.\n\nRecovery starts by taking that pressure away. At The Diamond, you stabilise under round-the-clock clinical supervision while sleep, appetite and mood recover from the crash, then work one-to-one with our therapists on the thinking and habits that kept the cycle turning. Daily holistic work, from yoga and personal training to meditation and massage, rebuilds energy and focus naturally, all within a calm, private residence in Hua Hin.",
			],
			'phases' => [
				'heading' => 'Three pillars of Ritalin recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A supervised, steady start',
						'paragraphs' => [
							'Ritalin withdrawal is rarely dangerous, but the crash is real.',
							'When regular misuse stops, the body answers with deep fatigue, low mood, disturbed sleep and strong cravings, which is when most unsupported attempts fail. In our fully equipped detox centre you stabilise under 24-hour care while our medical team focuses on restoring sleep, appetite and mood, so you reach therapy rested rather than running on empty.',
						],
						'listItems' => [
							'24-hour nursing and medical oversight',
							'Sleep and mood restoration through the crash',
							'Daily monitoring as your system rebalances',
						],
						'asideQuote' => '"Stimulant withdrawal is less about danger and more about endurance. Our job is to carry you through the crash so relapse never gets its opening."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Rewiring the habit of reaching for a pill',
						'paragraphs' => [
							'Detox clears the drug. Therapy addresses why you needed it.',
							'Working one-to-one with our psychologists, you use cognitive behavioural therapy to unpick the distortions that drive misuse: the belief that you cannot focus, perform or cope without it. You learn practical problem-solving techniques, healthier ways to manage stress and workload, and a realistic plan for the situations that used to send you back to the pills.',
						],
						'listItems' => [
							'Daily one-to-one cognitive behavioural therapy',
							'Tools for stress, focus and performance pressure',
							'A personal relapse-prevention plan',
						],
						'asideQuote' => '"Most of our Ritalin clients are capable, driven people. Therapy shows them the drug was never the source of that, and never has to be again."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Energy and focus, rebuilt naturally',
						'paragraphs' => [
							'Lasting recovery needs more than abstinence.',
							'Alongside therapy, your days include yoga, personal training, meditation and massage, habits that restore the energy and concentration Ritalin once promised artificially. Before you leave, we set up your aftercare: weekly online sessions with the counsellor who treated you, or an introduction to a trusted partner near home, so the structure continues after Hua Hin.',
						],
						'listItems' => [
							'Yoga, personal training, meditation and massage',
							'Healthy routines you take home with you',
							'Weekly aftercare with your own counsellor',
						],
						'asideQuote' => '"The goal is simple: by the time you leave, your best focus and energy come from your own routines, not from a prescription bottle."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Residential treatment removes you from the deadlines, prescriptions and pressures that keep Ritalin misuse going. At our private estate in Hua Hin you live in your own villa with 24-hour specialised care, a full clinical and therapeutic team around you, and the time and quiet to focus entirely on recovery. With a strict cap on guest numbers, our staff-to-client ratio stays high, your program stays personal, and your stay stays completely discreet.',
			],
			'prose' => [
				'heading' => 'Is it time to consider Ritalin rehab?',
				'paragraphs' => [
					'Ritalin is the brand name of methylphenidate, a prescription central nervous system stimulant most commonly used to treat attention deficit hyperactivity disorder and narcolepsy. It works on dopamine and norepinephrine, the brain chemicals involved in concentration, impulsivity and motivation. Taken as prescribed by a diagnosed individual, it helps steady focus and behaviour. The problems begin when it is used outside those boundaries.',
					'Ritalin carries a high potential for misuse precisely because of how it works. In someone without ADHD, the same dopamine boost produces euphoria and a sense of sharpened performance rather than balance. People who misuse it typically take far larger doses than a doctor would prescribe, sometimes crushing tablets to snort or inject them for a stronger effect. Tolerance builds, doses climb, and once physical dependence sets in, cutting back triggers fatigue, depression and cravings that pull users straight back to the drug.',
					'Misuse often escalates quietly. Students and professionals lean on Ritalin to study or work longer, which has made it one of the most misused prescription drugs, and what starts as an occasional boost becomes something the working week cannot run without. Over time, heavy use brings anxiety, insomnia, appetite loss and paranoia, while taking too much strains the heart and, in some cases, can be fatal. The cost eventually reaches sleep, health, relationships and the very performance the drug was meant to protect.',
					'If any of this feels familiar, that recognition is the most important step, and you do not have to take the next one alone. The Diamond Rehab Thailand offers personalised, medically supervised Ritalin addiction treatment in complete privacy, built around your circumstances rather than a standard template. Contact our admissions team for a confidential conversation about what your recovery could look like.',
				],
			],
			'faqCptIds' => [ 218, 191, 214, 173, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		1001 => [
			'slug' => 'prescription',
			'programTag' => 'prescription drug program',
			'hero' => [
				'eyebrow' => 'Prescription drug addiction treatment · Hua Hin',
				'headline' => 'Break the grip of prescription medication, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Medical detox, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating prescription drug addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Dependence on prescription medication often grows quietly out of a legitimate prescription. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of prescription drug addiction',
				'card1Items' => [
					'Needing higher doses to get the same effect as your tolerance builds',
					'Running out of medication early, requesting refills ahead of schedule or seeing multiple doctors',
					'Taking the medication to feel normal rather than to treat the original problem',
					'Spending more and more time thinking about the next dose',
					'Hiding how much you take from family, friends or your own doctor',
				],
				'card2Title' => 'Withdrawal symptoms during detox',
				'card2Items' => [
					'Flu-like symptoms when stopping opioid painkillers: muscle spasms, restlessness and diarrhea',
					'Rebound anxiety and insomnia when benzodiazepines are reduced, with seizures possible in severe cases',
					'A heavy crash after stopping stimulants: fatigue, low mood and long hours of sleep',
					'Depression and difficulty concentrating in the first days and weeks',
					'Strong cravings for the medication, often strongest when symptoms peak',
				],
			],
			'holistic' => [
				'eyebrow' => 'Our approach',
				'heading' => 'A holistic approach to prescription drug recovery',
				'body' => "Prescription drug dependence rarely starts with a bad decision. It usually grows out of a legitimate prescription: a painkiller after surgery, a benzodiazepine for anxiety, a stimulant for focus, a pill to finally sleep. Because the medication came from a doctor and sits in a labelled bottle, the problem is hard to recognise, and because the body adapts to it, willpower alone is rarely enough to quit.\n\nOur program meets each medication class on its own terms. Detox is medically supervised and designed around what you have been taking, followed by one-on-one therapy that works on the issues underneath the dependence, and wellness practices that rebuild sleep, calm and focus without a prescription. All of it happens in a private, resort-style setting in Hua Hin, with a clinical team beside you at every step.",
			],
			'phases' => [
				'heading' => 'Three pillars of prescription drug recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Medical detox',
						'h3' => 'A safe, supervised withdrawal',
						'paragraphs' => [
							'Stopping prescription medication abruptly can be painful, and with some drug classes, dangerous.',
							'Our medical team designs detox around the medication itself: a gradual taper for benzodiazepines, medication-assisted withdrawal for opioid painkillers, and supported rest through a stimulant crash. You are monitored around the clock in a private, controlled environment, with medication prescribed to ease discomfort where appropriate.',
						],
						'listItems' => [
							'Round-the-clock care from a certified medical team',
							'Tapered or medication-assisted protocols by drug class',
							'Comfort medication to ease withdrawal symptoms',
						],
						'asideQuote' => '"Withdrawal is different for everyone. Our job is to keep it safe, and to make it as comfortable as it can be."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Understanding why the medication took hold',
						'paragraphs' => [
							'Clearing the drug from your body is the start. The deeper work is understanding why you came to depend on it.',
							'In private one-on-one sessions, our therapists help you examine the moments that led to the dependence and the cues, cravings and automatic responses that keep it going. You leave with practical strategies for managing pain, anxiety and sleep without reaching for the bottle, and for reducing the risk of relapse long after you go home.',
						],
						'listItems' => [
							'Private one-on-one therapy sessions',
							'Work on the issues beneath the addiction',
							'Practical relapse-prevention strategies',
						],
						'asideQuote' => '"Stopping the medication is the beginning. Understanding why you needed it is the recovery."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Rebuilding calm without a prescription',
						'paragraphs' => [
							'Many people first took these medications to manage anxiety, pain or sleep. Recovery means finding other ways to meet those needs.',
							'Mindfulness meditation is woven into your daily routine, guided by experienced coaches, alongside wellness practices that restore the rest and stability the medication once supplied. Before you leave, we build a structured aftercare plan so the foundation you set in Hua Hin holds at home.',
						],
						'listItems' => [
							'Guided mindfulness meditation coaching',
							'Wellness practices built into the daily routine',
							'A structured aftercare plan for life at home',
						],
						'asideQuote' => '"Mindfulness puts a pause between an impulse and a decision. That pause is where recovery lives."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Quitting prescription medication at home is hard for a simple reason: the supply is rarely far away, and the routines that built the dependence are everywhere you look. Inpatient treatment at The Diamond removes you from refill cycles, prescribing doctors and daily triggers, and places you in a calm, private setting where nothing distracts from recovery. With a maximum of twelve clients, your detox, therapy schedule and daily rhythm are shaped around you, never around a template.',
			],
			'prose' => [
				'heading' => 'Is it time to consider prescription drug rehab?',
				'paragraphs' => [
					"Prescription drug addiction is dependence on medication that was, in most cases, originally prescribed by a doctor. The main classes are opioid painkillers such as oxycodone, hydrocodone, codeine, tramadol and fentanyl, benzodiazepines such as diazepam, alprazolam and lorazepam, stimulants such as methylphenidate and dextroamphetamine prescribed for ADHD, and sleeping pills. Abuse means using these medications in a way the prescribing doctor never intended: a higher dose, someone else's prescription, or taking the drug for its effects rather than the condition it was meant to treat.",
					'These medications can be addictive even when taken exactly as prescribed. The body adapts: tolerance builds, so the same dose does less, and higher doses are needed to get the original effect. Stop suddenly and withdrawal symptoms appear, which makes it harder to quit and easier to keep taking the drug just to feel normal. Because the medication comes from a doctor and is socially accepted, many people develop a dependence without realising they have a serious problem.',
					'Use tends to escalate in a recognisable pattern. Doses creep above what was prescribed. Refills are requested early, or the same prescription is sought from more than one doctor. When prescriptions run out, some people buy the medication without a prescription, and some who began with prescribed opioid painkillers move on to stronger and more dangerous opioids. By that point the original medical issue is no longer what is driving the use.',
					'If any of this feels familiar, in your own life or in someone you love, acknowledging it is the hardest step, and the most important one. Relapse rates for people who try to quit prescription drugs alone are high; a medically supervised detox followed by structured therapy gives recovery a far stronger footing. Our team at The Diamond Rehab Thailand can talk you through the admissions process confidentially and help you decide whether residential treatment is the right next step.',
				],
			],
			'faqCptIds' => [ 218, 204, 175, 213, 13219, 13220, 13221 ],
			'faqNew' => [],
		],
		1113 => [
			'slug' => 'depression',
			'programTag' => 'depression program',
			'hero' => [
				'eyebrow' => 'Depression treatment · Hua Hin',
				'headline' => 'Find your way back from depression, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating depression',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Depression builds slowly and is easy to dismiss as a rough patch. The signs show up in mood and in the body. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Emotional signs of depression',
				'card1Items' => [
					'Persistent sadness, emptiness or hopelessness',
					'Loss of interest in hobbies and once-loved activities',
					'Feelings of worthlessness or excessive guilt',
					'Social withdrawal and angry outbursts',
					'Anxiety and slowed thinking',
					'Recurrent thoughts of death, which deserve immediate care',
				],
				'card2Title' => 'Physical signs of depression',
				'card2Items' => [
					'Sleep disturbances, sleeping too little or too much',
					'Lack of energy and constant fatigue',
					'Appetite and weight changes',
					'Unexplained aches and physical problems',
					'Slowed movement or restlessness',
					'Trouble concentrating and completing tasks',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Psychiatric care and proven psychotherapies including CBT, DBT and trauma work, alongside fitness, nutrition and mindfulness that lift the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist around your history and your depression, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care on the hardest days', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, through the night and the lowest moments.' ],
			],
			'holistic' => [
				'eyebrow' => 'An illness, not a weakness',
				'heading' => 'A holistic approach to depression',
				'body' => "Depression is an illness, not a weakness, and pushing through alone rarely works. It drains the very energy, sleep and clear thinking you would normally use to lift yourself out, which is why willpower and time off so often fall short.\n\nYou'll move through a thorough psychiatric assessment into daily psychotherapy, one-to-one and in small groups, supported by fitness, mindfulness and nutrition, all inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of depression recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A calm, careful start',
						'paragraphs' => [
							'Depression rarely lifts on a schedule, and the first days are about making life feel manageable again.',
							'Our psychiatrist begins with a comprehensive psychiatric assessment to confirm the diagnosis and understand what sits beneath it, then builds a gentle early routine of rest, sleep and structure. Where appropriate, any medication is reviewed and adjusted by the psychiatrist, always as a support to therapy rather than the whole answer.',
						],
						'listItems' => [
							'Comprehensive psychiatric assessment on arrival',
							'A steadying routine of rest, sleep and structure',
							'Medication review by a psychiatrist where appropriate',
						],
						'asideQuote' => '"The first task is not to fix everything at once. It is to make the days feel manageable again, so the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Getting to the roots of the low',
						'paragraphs' => [
							'Psychotherapy is the most effective core treatment for depression, and it sits at the centre of your days here.',
							'Through daily one-to-one sessions and small group work, you will learn to recognise the negative thinking patterns that feed depression and replace them with workable alternatives, using cognitive behavioural therapy, DBT and trauma-focused work where your history calls for it.',
						],
						'listItems' => [
							'Daily one-to-one sessions with experienced therapists',
							'CBT, DBT and trauma-focused therapy',
							'Small group work to ease isolation',
						],
						'asideQuote' => '"Depression convinces you the bleakness is the truth. Therapy teaches you to question that voice, and then to answer it."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Recovery holds when mind and body are rebuilt together.',
							'Fitness, mindfulness, sleep and nutrition become part of your daily rhythm, and before you leave we design a personal aftercare plan, with ongoing therapy and practical strategies, so the progress you make here travels home with you.',
						],
						'listItems' => [
							'Fitness, mindfulness, sleep and nutrition every day',
							'A personal aftercare plan before you leave',
							'Practical strategies for the months ahead',
						],
						'asideQuote' => '"Leaving is not the end of treatment. You go home with a plan, and with people who still answer when you call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover at home means waking every day inside the same environment that feeds the low. A residential retreat puts real distance between you and that environment and replaces it with structure: daily light, movement and rest, regular therapy and round-the-clock support, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider a depression retreat?',
				'paragraphs' => [
					"Clinical depression, or major depressive disorder, is far more than sadness. It is a recognised medical illness that affects mood, thinking and the body. The Mayo Clinic's list of common symptoms runs from feelings of worthlessness and loss of interest in once-loved activities to sleep disturbance, appetite changes, slowed thinking, unexplained physical problems and recurrent thoughts of death.",
					"What separates depression from an ordinary low mood is duration, depth and loss of function. A rough patch lifts within days; depression persists for weeks, drains pleasure from things you once enjoyed and begins to interfere with work, relationships and the ability to care for yourself. Researchers point to two forces behind it: a systematic negative bias in thinking, and the patterns of a person's interaction with their environment. Cognitive behavioural therapy is so central to treatment because it works on both.",
					'Left untreated, depression tends to deepen. Sleep disturbance worsens the low and the low worsens sleep, isolation grows, and some people turn to alcohol or other risky behaviours hoping to numb the symptoms, which only entrenches them. The encouraging news is that depression responds well to professional treatment. Psychotherapy is the most effective core approach, and a meta-analysis in Psychiatry Research found cognitive behavioural therapy superior to medication for treatment-resistant depression, with medication helping as an adjunct to therapy when a psychiatrist recommends it.',
					'Acknowledging that you are struggling takes real courage, and you do not have to find the way back on your own. If depression has settled over your life, or the life of someone you love, and standard measures have not been enough, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'How is depression treated at a residential retreat?',
					'answer' => 'Treatment moves through clear steps: a thorough psychiatric evaluation to confirm the diagnosis, a personalised treatment plan, then daily psychotherapy supported by holistic practice, with the plan reviewed and adjusted as you progress. Psychotherapy is the core of the program, one-to-one and in small groups, alongside complementary approaches such as yoga, meditation and other relaxation techniques. Before discharge we prepare a personal aftercare plan so your progress continues at home.',
				],
				[
					'question' => 'Do I need medication to recover from depression?',
					'answer' => 'Not necessarily. Medication can ease depressive symptoms by acting on neurotransmitters such as serotonin, but it should not be the main component of depression treatment; it works best as an adjunct to therapy. Research published in Psychiatry Research found cognitive behavioural therapy superior to medication for treatment-resistant depression. Our psychiatrist reviews your situation, and any existing prescriptions, and recommends medication only where it genuinely supports your recovery.',
				],
				[
					'question' => 'How long does depression treatment take?',
					'answer' => 'It depends on the severity of your symptoms and your personal circumstances. Residential programs typically run for 30, 60 or 90 days, and a psychiatrist will recommend the right length of stay after your initial assessment. Recovery does not end at discharge: you leave with an aftercare plan, including ongoing therapy where appropriate, to maintain your results.',
				],
			],
		],
		1105 => [
			'slug' => 'anxiety',
			'programTag' => 'anxiety program',
			'hero' => [
				'eyebrow' => 'Anxiety treatment · Hua Hin',
				'headline' => 'Find calm again, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating anxiety disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Everyone feels anxious from time to time, so a disorder is easy to mistake for ordinary stress. The signs show up in the mind and in the body. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Emotional signs of anxiety',
				'card1Items' => [
					'Sudden feelings of panic and intense fear',
					'Chronic worry about nonspecific events',
					'Restlessness and a mind that will not switch off',
					'Fear of being judged or embarrassed in social situations',
					'Avoiding places that feel difficult to escape from',
					'Obsessive thoughts that drive repetitive behaviour',
				],
				'card2Title' => 'Physical signs of anxiety',
				'card2Items' => [
					'Increased heart rate and a racing pulse',
					'Shortness of breath',
					'Excessive sweating',
					'Headaches and dry mouth',
					'Insomnia and disturbed sleep',
					'Nausea and an unsettled stomach',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Psychiatric care and proven psychotherapies including CBT, DBT and mindfulness-based work, alongside fitness, nutrition and meditation that steady the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist around your history and your anxiety, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care on the hardest days', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, through the night and the most anxious moments.' ],
			],
			'holistic' => [
				'eyebrow' => 'An illness, not a weakness',
				'heading' => 'A holistic approach to anxiety',
				'body' => "An anxiety disorder is a treatable illness, not a personal failing, and white-knuckling through it alone rarely works. Anxiety feeds on its own symptoms: a racing heart and shallow breath feel like confirmation that something is wrong, which only winds the worry tighter.\n\nYou'll move through a thorough psychiatric assessment into daily psychotherapy, one-to-one and in small groups, supported by mindfulness, fitness and nutrition, all inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of anxiety recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A calm, careful start',
						'paragraphs' => [
							'Anxiety disorders differ considerably from person to person, so treatment begins with understanding yours.',
							'Our psychiatrist starts with a comprehensive psychiatric assessment to identify the causes, symptoms and triggers that characterise your specific disorder, then builds a gentle early routine of rest, sleep and structure. Where appropriate, any medication is reviewed and adjusted by the psychiatrist, always as a support to therapy rather than the whole answer.',
						],
						'listItems' => [
							'Comprehensive psychiatric assessment on arrival',
							'A steadying routine of rest, sleep and structure',
							'Medication review by a psychiatrist where appropriate',
						],
						'asideQuote' => '"The first task is not to silence every worry at once. It is to make the days feel manageable again, so the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Reprogramming the anxious mind',
						'paragraphs' => [
							'Evidence-based psychotherapy sits at the centre of your days here, tailored to your diagnosis rather than a standard script.',
							'Through daily one-to-one sessions and small group work, you will learn to identify and reprogram the dysfunctional thought patterns that feed anxiety, using cognitive behavioural therapy, DBT, mindfulness therapy and carefully paced exposure-based work where your disorder calls for it.',
						],
						'listItems' => [
							'Daily one-to-one sessions with experienced therapists',
							'CBT, DBT, mindfulness and exposure-based therapy',
							'Small group work to ease isolation',
						],
						'asideQuote' => '"Anxiety insists the danger is real and unbearable. Therapy teaches you to question that voice, and then to answer it."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Recovery holds when mind and body learn to settle together.',
							'Meditation, breathing exercises, fitness and nutrition become part of your daily rhythm, and before you leave we design a personal aftercare plan, with ongoing therapy and practical coping techniques, so the calm you build here travels home with you.',
						],
						'listItems' => [
							'Meditation, breathing practice and fitness every day',
							'A personal aftercare plan before you leave',
							'Practical coping techniques for the months ahead',
						],
						'asideQuote' => '"Leaving is not the end of treatment. You go home with a full set of coping tools, and with people who still answer when you call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover at home means facing the same stressors and triggers that keep the worry running. A residential retreat puts real distance between you and that environment and replaces it with structure: a peaceful daily rhythm of therapy, movement and rest, with round-the-clock support from dedicated mental health professionals, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider anxiety treatment?',
				'paragraphs' => [
					'Anxiety disorders are a category of mental health conditions characterised by extreme feelings of anxiety, fear or worry that can persist for extended periods of time. They take many forms: generalised anxiety disorder, the most common, brings chronic worry about nonspecific events; panic disorder produces sudden bursts of intense fear; social anxiety disorder centres on a fear of being judged or embarrassed; agoraphobia is a fear of situations that would be difficult to escape from; and obsessive-compulsive disorder pairs intrusive thoughts with repetitive behaviour. Anxiety is one of the most common mental health problems in the world, affecting about 264 million people globally.',
					"Anxiety itself is a normal human response to stress. It's natural to feel nervous before a job interview, an important presentation or a move to another country, and that kind of anxiety usually passes without leaving a mark. An anxiety disorder is different: the fear is out of proportion, it persists long after the stressful moment has gone, and it begins to interfere with work, relationships and daily life.",
					"Without proper treatment, an anxiety disorder tends to deepen rather than fade. It wears away at mood, attitude and behaviour, and the physical symptoms can be frightening in their own right: a racing heart, sweating, shortness of breath and nausea can mimic a cardiac event, even though anxiety itself cannot kill you. The encouraging news is that anxiety disorders respond well to professional care. Evidence-based psychotherapy, tailored to your diagnosis and delivered consistently, helps you challenge the distorted thinking beneath the fear and build practical skills to manage stress, which is exactly what a structured residential program is designed to provide.",
					'Acknowledging that the worry has grown bigger than you can manage takes real courage, and you do not have to find the way back on your own. If anxiety has settled over your life, or the life of someone you love, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 32, 173, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Can you go to rehab for anxiety?',
					'answer' => 'Yes. Residential treatment is widely regarded as one of the most effective ways to treat an anxiety disorder. An inpatient program offers a peaceful, controlled environment where you can focus fully on your recovery, away from the distractions and demands of the outside world, while receiving 24-hour support from dedicated mental health professionals.',
				],
				[
					'question' => 'What type of therapy is best for anxiety disorders?',
					'answer' => "Given how considerably anxiety disorders differ, there is no single best treatment. Therapy is tailored to your diagnosis, symptoms and unique needs, and several techniques may be used alone or in combination, including cognitive behavioural therapy, dialectical behaviour therapy, exposure therapy, mindfulness therapy and interpersonal therapy. Your psychiatrist and therapists will recommend the right mix after your initial assessment.",
				],
				[
					'question' => 'Can anxiety kill you?',
					'answer' => 'No, although it can feel like it sometimes. Anxiety can induce episodes of intense fear and cause a range of unpleasant symptoms, including an increased heart rate, excessive sweating, shortness of breath and nausea. These symptoms can mimic cardiac events, which may make you feel as though you are in danger. Nevertheless, anxiety cannot kill you, and with proper treatment the episodes themselves become far less frequent and frightening.',
				],
			],
		],
		1109 => [
			'slug' => 'ptsd',
			'programTag' => 'trauma program',
			'hero' => [
				'eyebrow' => 'PTSD & trauma treatment · Hua Hin',
				'headline' => 'Make peace with the past, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Psychiatric assessment, evidence-based trauma therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating trauma and PTSD',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Trauma rarely stays in the past on its own. The signs can surface within weeks of an event, or take months and even years to appear. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of PTSD',
				'card1Items' => [
					'Uncontrollable thoughts and painful memories of the event',
					'Reliving the experience through realistic flashbacks',
					'Vivid nightmares about what happened',
					'Avoiding people, places and things that bring the event back',
					'Hypervigilance and feeling constantly on guard',
					'Persistent fearfulness and anxiety',
				],
				'card2Title' => 'How trauma shows up day to day',
				'card2Items' => [
					'Insomnia and unrefreshing sleep',
					'Mood swings and irritability',
					'Loss of focus and memory problems',
					'Feeling emotionally detached from people around you',
					'Feeling jumpy, on edge and easily startled',
					'Feelings of guilt and shame',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Psychiatric care and proven trauma-focused therapies including EMDR, CBT and mindfulness practice, alongside yoga, meditation and exercise therapy that heal the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'Trauma takes many shapes, and no single prescriptive approach works for everyone. With only twelve clients on site, your plan is built by a psychiatrist around your history and your trauma, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care on the hardest days', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, through the night and the most difficult moments.' ],
			],
			'holistic' => [
				'eyebrow' => 'An injury, not a weakness',
				'heading' => 'A holistic approach to trauma',
				'body' => "Trauma is an injury, not a weakness, and time alone rarely heals it. The mind protects itself by avoiding what happened while the body stays braced for danger, so the wound is carried rather than closed. Willpower and distraction can hold things together for a while, but they do not resolve the unresolved trauma that lies at the heart of PTSD.\n\nYou'll move through a thorough psychiatric assessment into trauma-focused therapy, paced carefully and never faster than feels safe, supported by yoga, meditation and exercise therapy, all inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of trauma recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A safe, steady start',
						'paragraphs' => [
							'Trauma treatment never begins with the trauma itself. It begins with feeling safe.',
							'Our psychiatrist starts with a comprehensive assessment to understand your symptoms and what sits beneath them, including whether the trauma was a single event or repeated over time. From there we build a gentle early routine of rest, sleep and structure, and any medication is reviewed by the psychiatrist where appropriate, always as a support to therapy rather than the whole answer.',
						],
						'listItems' => [
							'Comprehensive psychiatric assessment on arrival',
							'A steadying routine of rest, sleep and structure',
							'Medication review by a psychiatrist where appropriate',
						],
						'asideQuote' => '"Nothing useful happens until you feel safe. We build that foundation first, and only then begin the deeper work."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Processing trauma at your pace',
						'paragraphs' => [
							'Unresolved trauma lies at the heart of PTSD, and processing it safely is the centre of your days here.',
							'Through one-to-one sessions with experienced trauma therapists, you will explore, process and heal what happened using trauma-focused therapies such as EMDR and trauma-focused CBT, confronting the memory in a controlled way and learning to recognise and change the thinking patterns it left behind.',
						],
						'listItems' => [
							'One-to-one sessions with experienced trauma therapists',
							'EMDR and trauma-focused CBT',
							'Practical skills for managing symptoms day to day',
						],
						'asideQuote' => '"You set the pace. The work is always done in a controlled way, never faster than feels safe."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Recovery holds when mind and body learn to feel safe together.',
							'Yoga, meditation and exercise therapy become part of your daily rhythm, and you will practise the coping skills that keep symptoms manageable in a healthy, sustainable way. Before you leave we design a personal aftercare plan, so the steadiness you build here travels home with you.',
						],
						'listItems' => [
							'Yoga, meditation and exercise therapy every day',
							'Practical coping skills you keep for life',
							'A personal aftercare plan before you leave',
						],
						'asideQuote' => '"You leave with a stronger understanding of yourself and the tools to live with peace and stability."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to heal from trauma at home means staying surrounded by the reminders and stressors of everyday life. A residential program provides a safe, structured space that insulates you from those triggers and replaces them with routine, privacy and round-the-clock support, so your undivided attention can go into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider PTSD treatment?',
				'paragraphs' => [
					'Post-traumatic stress disorder, or PTSD, is a mental health condition that some people develop after experiencing or witnessing a traumatic event. It brings uncontrollable thoughts and painful memories of the experience, and many people relive the event through realistic flashbacks and nightmares. Some develop complex PTSD, which follows repeated trauma rather than a single event and calls for the same careful, professional treatment.',
					'Almost any situation involving real danger can lead to PTSD, and it is still not fully understood why some people are affected and others are not; about 30 percent of people who experience severe trauma go on to develop it. What separates PTSD from a normal stress response is what happens afterwards. The body reacts to danger by releasing stress hormones such as adrenaline and norepinephrine as part of its natural fight-or-flight response, and normally balance is restored once the event has passed. In PTSD, that state of hyperarousal persists long after the danger is over.',
					'Symptoms can develop within a few weeks of a traumatic event, or take months and even years to appear, and they tend to worsen without treatment. Left unaddressed, PTSD can change the way you think and feel, straining relationships, careers and day-to-day life, while prolonged hyperarousal feeds anxiety, insomnia and feelings of guilt and may affect the hippocampus, a part of the brain involved in emotional processing. The encouraging news is that PTSD responds to professional care: trauma-focused therapies such as EMDR and cognitive behavioural therapy help you process the event in a controlled way, and a meta-analysis of 42 studies in Clinical Psychology Review found that around 44 percent of people fully recover after receiving treatment.',
					'Reaching out about trauma takes real courage, and you do not have to carry it on your own. If a past event still shapes your days, or the days of someone you love, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of trauma therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 32, 183, 198, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'How long does it take to recover from PTSD?',
					'answer' => 'The recovery timeline varies significantly from person to person, depending on genetic factors, environment, the type of trauma and whether treatment has been sought. Some people recover within about six months; for others it can take longer. Residential treatment typically runs for 30 to 90 days, and a psychiatrist will recommend the right length of stay after your initial assessment.',
				],
				[
					'question' => 'What is the most effective therapy for PTSD?',
					'answer' => 'PTSD affects everyone differently, so the most effective method for one person may not be the best option for another. Depending on your needs, your therapist may draw on cognitive behavioural therapy, prolonged exposure therapy, cognitive processing therapy, mindfulness therapy or EMDR. CBT, one of the most widely used, helps you recognise and modify the thinking patterns that sustain PTSD and confront the trauma in a controlled manner.',
				],
				[
					'question' => 'What is the success rate of PTSD treatment?',
					'answer' => 'A meta-analysis of 42 studies, published in Clinical Psychology Review, found that about 44 percent of people with PTSD fully recover after receiving treatment. Symptoms tend to worsen over time without professional care, which is why seeking effective treatment as early as possible matters.',
				],
			],
		],
		1141 => [
			'slug' => 'burnout',
			'programTag' => 'executive burnout program',
			'hero' => [
				'eyebrow' => 'Executive burnout treatment · Hua Hin',
				'headline' => 'Recover your energy, and yourself, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating executive burnout',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Burnout creeps up on capable people, and high performers are often the last to admit it. The signs show up in mood and in the body. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Mental and emotional signs of burnout',
				'card1Items' => [
					'Persistent low mood and feelings of hopelessness',
					'Cynicism, pessimistic thoughts and loss of job satisfaction',
					'Loss of enthusiasm and motivation for work you once cared about',
					'Irritability, anger or sudden mood swings',
					'Declining performance and an inability to focus',
					'Dreading the working day and thinking about resigning',
				],
				'card2Title' => 'Physical signs of burnout',
				'card2Items' => [
					'Chronic fatigue that rest never seems to fix',
					'Sleep disturbance and struggling to wake on time',
					'Frequent headaches',
					'Aches and sensations of pain elsewhere in the body',
					'Digestive problems',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Clinical assessment and proven therapies including CBT, counselling and stress management training, alongside fitness, spa treatments and mindfulness that restore the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your program is a bespoke combination of therapies built around your situation and your career, never a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Discretion executives can rely on', 'body' => 'A 4:1 staff-to-client ratio, 24/7 support and a private coastal retreat with the calm of a 5-star hotel, offering the discretion and flexibility a demanding career requires.' ],
			],
			'holistic' => [
				'eyebrow' => 'The body calling time',
				'heading' => 'A holistic approach to burnout',
				'body' => "Burnout is not a weakness. It is the body calling time after months or years of chronic workplace stress, and it drains the very energy, sleep and focus you would normally use to push through, which is why pushing through only makes it worse.\n\nYou'll move through a thorough clinical assessment into one-to-one psychotherapy on the drivers beneath the exhaustion, from perfectionism to boundaries to chronic stress, supported by deep rest, fitness and mindfulness, all inside a calm, private coastal setting designed to make recovery possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of burnout recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Deep rest comes first',
						'paragraphs' => [
							'Burnout does not lift over a long weekend, and the first days here are about genuine rest.',
							'Our clinical team begins with a comprehensive assessment to understand how deep the exhaustion runs and what sits beneath it, then builds an early routine of deep rest, sleep restoration and gentle structure, putting real distance between you and the pressures that brought you here.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'Deep rest and sleep restoration before anything else',
							'A gentle early routine away from work pressure',
						],
						'asideQuote' => '"The first task is not to solve your career. It is to let an exhausted system properly rest, so the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Working on what drives the burnout',
						'paragraphs' => [
							'Rest alone is not recovery. Without addressing the patterns that produced the exhaustion, burnout tends to come back.',
							'Through one-to-one sessions and small group work, you will examine the drivers beneath it, perfectionism, weak boundaries and chronic stress, using cognitive behavioural therapy, counselling and stress management training to rebuild an identity that is bigger than your job title.',
						],
						'listItems' => [
							'One-to-one cognitive behavioural therapy and counselling',
							'Stress management training and small group sessions',
							'Boundaries, perfectionism and identity beyond work',
						],
						'asideQuote' => '"Burnout is rarely about weakness. It is about capable people running on patterns that stopped serving them years ago."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for the return to work',
						'paragraphs' => [
							'Recovery holds when mind and body are rebuilt together, and when going back does not mean going back to the same habits.',
							'Fitness, mindfulness, nutrition and spa treatments become part of your daily rhythm, and before you leave we design sustainable routines and a practical return-to-work plan, so the energy you recover here lasts well beyond the retreat.',
						],
						'listItems' => [
							'Fitness, mindfulness and nutrition woven into every day',
							'Sustainable routines you can keep at home',
							'A practical return-to-work plan before you leave',
						],
						'asideQuote' => '"Leaving rested is the easy part. Leaving with routines that protect that energy is what we are really here for."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover from burnout at home means staying within reach of the office, the inbox and the expectations that caused it. A residential retreat puts real distance between you and those pressures and replaces them with rest, structure and round-the-clock support, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider burnout treatment?',
				'paragraphs' => [
					'Everybody feels stressed at work from time to time, but burnout is something more. Only relatively recently classified by the World Health Organisation as a serious occupational health hazard, burnout syndrome is a state of physical, mental and emotional exhaustion caused by chronic workplace stress, with common drivers including long working hours, tight deadlines, job insecurity, a lack of leadership and feeling undervalued or unfairly compensated.',
					'What separates burnout from ordinary tiredness is that rest stops working. The exhaustion persists after the demanding quarter ends, enthusiasm and focus do not return, and cynicism settles in where commitment used to be. Burnout is also often mistaken for depression; while chronic workplace stress can cause or worsen depression, burnout is tied specifically to work, yet its effects can spill over into almost every aspect of your life.',
					'Left unaddressed, burnout tends to deepen. Sleep disturbance, frequent headaches and chronic fatigue take hold, performance and motivation slide, and many people become unmotivated, depressed and physically unwell. Short breaks, time away from digital devices and new hobbies can help in the early stages, but once chronic stress has become entrenched, recovering without specialist help can seem next to impossible. A structured residential program works because it combines clinical assessment, cognitive behavioural therapy and stress management with genuine rest, treating the exhaustion and its causes at the same time.',
					'Acknowledging that you are running on empty takes real honesty, especially for people used to being the one others rely on. If burnout has settled over your work and your life, or you are worried about a colleague or employee, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 173, 32, 175, 183, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'How long does it take to recover from burnout?',
					'answer' => "It depends on the severity of the burnout and how long it has been neglected. Some people recover within a few weeks or months following residential treatment, while for others the process is ongoing and can last years. Our clinical team assesses your situation on arrival and recommends a length of stay that fits how deep the exhaustion runs, and you leave with sustainable routines and a return-to-work plan so progress continues at home.",
				],
				[
					'question' => 'What are the five stages of burnout?',
					'answer' => 'The five commonly observed stages are the Honeymoon Phase, the Onset of Stress, the Development of Chronic Stress, Burnout, and eventually Habitual Burnout. The earlier you act, the easier recovery is; if you frequently feel chronically stressed or burnt out at work, it is worth discussing your situation with a specialist before the later stages take hold.',
				],
				[
					'question' => 'How is burnout syndrome diagnosed?',
					'answer' => 'Too often, burnout goes undiagnosed, which can have devastating consequences. Many people are only diagnosed after visiting a doctor with physical symptoms such as headaches, digestive problems and sleep disorders. At The Diamond Rehab Thailand, treatment begins with a comprehensive clinical assessment so we understand exactly what you are dealing with before any program is designed.',
				],
			],
		],
		1135 => [
			'slug' => 'insomnia',
			'programTag' => 'sleep program',
			'hero' => [
				'eyebrow' => 'Insomnia & sleep treatment · Hua Hin',
				'headline' => 'Sleep well again, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based sleep therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating sleep disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Insomnia builds quietly, night by night, and the cost shows up long after dark. The signs appear in bed and follow you through the day. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Signs of chronic insomnia',
				'card1Items' => [
					'Difficulty falling asleep at night',
					'Waking in the night or too early in the morning',
					'Sleep that leaves you unrefreshed',
					'Worry and negative thoughts about sleep itself',
					'Disrupted sleep three or more nights a week, for months',
				],
				'card2Title' => 'What poor sleep does by day',
				'card2Items' => [
					'Fatigue that lingers through the daytime',
					'Irritability and anxiety',
					'Loss of concentration and focus',
					'Low mood and flattened energy',
					'A growing struggle to function properly at work and at home',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'CBT-I at the core, calm around it', 'body' => 'Cognitive behavioural therapy for insomnia (CBT-I) forms the basis of the program, alongside yoga, meditation and exercise that restore the whole person, not just the nights.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your sleep plan is built by our clinical team around your history and your nights, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care through the longest nights', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, including at three in the morning when sleep will not come.' ],
			],
			'holistic' => [
				'eyebrow' => 'More than a rough night',
				'heading' => 'A holistic approach to sleep',
				'body' => "Chronic insomnia is self-reinforcing. The harder you try to sleep, the more anxious the bed becomes, and the worry about not sleeping is precisely what keeps you awake. It also rarely travels alone: insomnia often stems from deeper medical or psychological issues, including anxiety, depression and unhealthy sleep habits, that must be addressed for lasting change.\n\nYou'll move through a thorough clinical and sleep assessment into CBT-I and one-to-one therapy on what sits beneath the sleeplessness, supported by steady routines, daylight, movement and a calm wind-down each evening, all inside a quiet, private setting designed for rest.",
			],
			'phases' => [
				'heading' => 'Three pillars of sleep recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A calm, careful start',
						'paragraphs' => [
							'Sleep cannot be forced back into place, and the first days are about taking the pressure off your nights.',
							'Our clinical team begins with a comprehensive assessment of your sleep and overall health, looking for the medical and psychological issues that often sit beneath insomnia, then sets a gentle, consistent daily routine. Where appropriate, any medication is reviewed by our doctors, always as a support to therapy rather than the whole answer.',
						],
						'listItems' => [
							'Comprehensive clinical and sleep assessment on arrival',
							'Screening for underlying conditions such as anxiety and depression',
							'Medication review where appropriate',
						],
						'asideQuote' => '"You cannot will yourself to sleep. The first task is to take the pressure off the nights, so the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Retraining the mind for sleep',
						'paragraphs' => [
							'Cognitive behavioural therapy forms the basis of our insomnia treatment program, because it works on the thoughts that keep you awake as well as the habits.',
							'In structured one-to-one sessions you will learn to recognise and challenge the negative thinking that fuels sleeplessness, while practising proven techniques such as stimulus control, sleep restriction, sleep hygiene training and relaxation skills you can carry into daily life.',
						],
						'listItems' => [
							'Structured CBT-I sessions with experienced therapists',
							'Stimulus control, sleep restriction and sleep hygiene training',
							'Relaxation techniques to quiet anxiety around sleep',
						],
						'asideQuote' => '"Insomnia feeds on the fear of another sleepless night. Therapy works on that fear until the bed stops being a battleground."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for the nights after you leave',
						'paragraphs' => [
							'A good night\'s sleep is built during the day, and recovery holds when the whole routine supports it.',
							'Daylight, movement, yoga, meditation and unhurried wind-down rituals become part of your daily rhythm, and before you leave we design a sustainable sleep plan, so the habits you build here keep working at home in the years ahead.',
						],
						'listItems' => [
							'Daylight, exercise, yoga and meditation woven into each day',
							'Calm wind-down rituals each evening',
							'A sustainable sleep plan to take home',
						],
						'asideQuote' => '"Good sleep is built in the daytime. You leave with a routine that protects it long after you go home."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to rebuild sleep at home means doing it inside the same bedroom, schedule and pressures that broke it. A residential stay gives you a consistent, quiet environment, days with genuine structure and no late-night demands, and clinical professionals on hand around the clock, so every part of the day is working towards better nights.',
			],
			'prose' => [
				'heading' => 'Is it time to consider insomnia treatment?',
				'paragraphs' => [
					'Insomnia is an extremely common sleep disorder that affects about one in three people globally. It shows up as trouble falling asleep, staying asleep or getting adequate amounts of quality rest. Clinicians distinguish acute insomnia, a short-term condition usually triggered by a stressful event that tends to resolve on its own, from chronic insomnia, defined as disrupted sleep at least three nights per week for three months or longer. It is the chronic form that benefits most from structured treatment.',
					'Chronic insomnia rarely has a single cause. High levels of stress and mental health conditions such as anxiety and depression are common drivers, as are stimulants including caffeine, nicotine and alcohol. Lifestyle plays its part too: shift work, an irregular sleep schedule, napping during the day and watching screens before bed can all keep the disorder going, and underlying medical issues sometimes sit beneath it.',
					'Left unaddressed, chronic insomnia drains mood, energy and concentration and erodes your ability to function during the day. Sleeping medication can be useful for managing symptoms, but it fails to address the underlying issues that cause the disorder. Cognitive behavioural therapy is generally regarded as the most effective treatment for insomnia: a therapist works with you to identify and modify the thoughts and beliefs that fuel sleeplessness, alongside practical techniques such as stimulus control, sleep restriction and sleep hygiene training.',
					'If your insomnia has lasted longer than two weeks, or has started to affect your ability to function in day-to-day life, it is worth speaking to a doctor. Acknowledging that sleep has become a problem takes honesty, and you do not have to solve it on your own. A quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of sleep therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 32, 3443, 3430, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What are the three types of insomnia?',
					'answer' => 'There are three main types. Acute insomnia is a short-term condition, typically caused by a stressful life event, and usually resolves on its own. Chronic insomnia is a long-term condition defined as disrupted sleep occurring at least three nights per week for three months or longer. Comorbid insomnia occurs alongside another condition, such as anxiety or depression, which is why a thorough assessment matters before treatment begins.',
				],
				[
					'question' => 'What is the best treatment for insomnia?',
					'answer' => 'Cognitive behavioural therapy is generally regarded as the most effective way to treat insomnia. Your therapist works with you to identify and modify the thoughts and beliefs that fuel insomnia, and helps you develop practical techniques for improving sleep habits, including stimulus control, sleep restriction and sleep hygiene training. Medication can be useful for managing symptoms, but it fails to address the underlying issues that cause the disorder.',
				],
				[
					'question' => 'When should I seek help for insomnia?',
					'answer' => 'You should see a doctor if your insomnia lasts longer than two weeks, or if it has started to affect your ability to function in day-to-day life. You should also seek help if you have been told that you snore loudly or momentarily stop breathing while sleeping, as these symptoms could indicate sleep apnea. For most people insomnia is not dangerous in itself, but it can have a major impact on quality of life, and it responds well to structured treatment.',
				],
			],
		],
		1120 => [
			'slug' => 'codependency',
			'programTag' => 'codependency program',
			'hero' => [
				'eyebrow' => 'Codependency treatment · Hua Hin',
				'headline' => 'Find your way back to yourself, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating codependency',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Codependency isn't always easy to recognise. Many people happily prioritise the needs of loved ones without realising the cost to themselves. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of codependency',
				'card1Items' => [
					'Low self-esteem and a strong desire to please people',
					'Struggling to set healthy, reasonable boundaries',
					"Feeling responsible for other people's problems",
					"Putting others' needs ahead of your own, at your own cost",
					'An excessive fear of rejection or abandonment',
					'Allowing or enabling unhealthy behaviour in a loved one',
				],
				'card2Title' => 'How codependency feels day to day',
				'card2Items' => [
					'Craving validation and approval from others',
					"Obsessing over other people's issues",
					'Difficulty with intimacy and honest communication',
					'Tolerating abuse or unfulfilling relationships',
					'Feeling unhappy or lost when alone',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Proven psychotherapies, including cognitive behavioural therapy and structured 12 step work, alongside mindfulness and holistic practice that restore balance to the whole person, not just the relationship pattern.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built around your history and your relationships, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care whenever you need it', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, including the moments when old patterns pull hardest.' ],
			],
			'holistic' => [
				'eyebrow' => 'A learned pattern, not a flaw',
				'heading' => 'A holistic approach to codependency',
				'body' => "Codependency is a learned behaviour, not a character flaw. For many people it begins in childhood, in dysfunctional family systems where a child takes on the role of caregiver or carries the blame for the family's troubles, and the habit of earning love through self-sacrifice follows them into adult relationships.\n\nYou'll move through a thorough clinical assessment into one-to-one and group psychotherapy focused on boundaries, self-worth and the relationship patterns beneath the behaviour, supported by mindfulness and holistic practice, all inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of codependency recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A calm, careful start',
						'paragraphs' => [
							'Codependency is rarely obvious from the inside, and the first days are about stepping back far enough to see it clearly.',
							'Our clinical team begins with a comprehensive assessment to understand your relationships, your history and the role codependency plays in your life, then builds a gentle early routine of rest and structure, with the time and breathing space that day-to-day life never allows.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'A steadying routine of rest and structure',
							'Time and breathing space away from daily pressures',
						],
						'asideQuote' => '"You cannot see a pattern clearly while you are still inside it. The first task is simply to step back."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Getting to the roots of the pattern',
						'paragraphs' => [
							'This is a journey of honesty and introspection, and psychotherapy sits at the centre of your days here.',
							'Under the expert supervision of our therapists, you will look objectively at your relationships, explore the unresolved early experiences that often sit beneath codependency, and learn, through cognitive behavioural therapy and small group work, to set reasonable boundaries and recognise the negative thought patterns that fuel codependent behaviour.',
						],
						'listItems' => [
							'One-to-one sessions with experienced therapists',
							'Cognitive behavioural therapy and structured group work',
							'Learning to set reasonable, healthy boundaries',
						],
						'asideQuote' => '"Codependency teaches you that love must be earned through self-sacrifice. Therapy teaches you that it does not."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Recovery holds when balance returns to your overall health and wellbeing, not just to your relationships.',
							'Mindfulness, holistic practice and structured routine become part of your day, and before you leave we design a personal aftercare plan, so you go home with the schematics for building a balanced, independent and rewarding life.',
						],
						'listItems' => [
							'Mindfulness and holistic practice woven into daily life',
							'A personal aftercare plan before you leave',
							'Practical skills for balanced, independent living',
						],
						'asideQuote' => '"You leave with more than insight. You leave with the skills to build an independent, rewarding life."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to change codependent patterns at home means staying inside the very relationships and routines that feed them. A residential retreat puts real distance between you and those dynamics and replaces them with time, breathing space and professional guidance, so all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider codependency treatment?',
				'paragraphs' => [
					"Codependency is a dysfunctional relationship pattern in which one person consistently puts another person's needs ahead of their own. Codependent people excessively crave validation from others and will do almost anything to gain their approval, even when that means enabling a loved one's unhealthy behaviour. It is sometimes referred to as relationship addiction, and it is considered a behavioural condition rather than a mental illness.",
					"Codependency is a learned behaviour. For many people it stems from childhood experiences in dysfunctional family systems: a child forced to take on the role of caregiver for a parent unable to care for themselves, or a child continuously blamed for the family's dysfunction, may develop codependent traits later in life. Where a partner is struggling with addiction, the dynamic often deepens, because being needed creates a false sense of love and intimacy that holds a one-sided relationship in place.",
					"Left unaddressed, codependency adversely affects both people in a relationship. The caretaker neglects their own needs, which can lead to low self-esteem, depression and a wide range of other physical and mental issues, while their unconditional support quietly enables the other person's unhealthy behaviour. The encouraging news is that codependency responds well to talk-based therapy. Through approaches such as cognitive behavioural therapy and structured 12 step work, people learn to recognise problematic behaviour, set reasonable boundaries and build healthier relationships, both with themselves and with those around them.",
					'Recognising codependency in yourself takes real honesty, precisely because from the inside it so often looks like love and loyalty. If these patterns feel familiar, in your own life or in a relationship you care about, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A clinician reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 173, 32, 191, 175, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is codependency a mental illness?',
					'answer' => 'No. Codependency is considered a behavioural condition and is sometimes referred to as relationship addiction. It is a learned pattern, which is encouraging news for treatment: what was learned can be unlearned, and talk-based therapies such as cognitive behavioural therapy help people recognise problematic behaviour, set reasonable boundaries and develop healthier relationships.',
				],
				[
					'question' => 'Can codependent people become narcissists?',
					'answer' => "Codependent people aren't necessarily at risk of developing into narcissists. While most narcissists can be classified as codependent, the inverse isn't true: most codependents are not exploitative or unempathetic, which means they are not considered narcissistic.",
				],
			],
		],
		1039 => [
			'slug' => 'gambling',
			'programTag' => 'gambling program',
			'hero' => [
				'eyebrow' => 'Gambling addiction treatment · Hua Hin',
				'headline' => 'Break the gambling cycle, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating gambling addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "For most people gambling stays a casual pastime, so the line between recreation and compulsion is easy to miss. The clearest difference is the urge to keep betting whether you are winning or losing. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of gambling addiction',
				'card1Items' => [
					'An overwhelming urge to gamble, whether you are winning or losing',
					'Placing increasingly larger bets to feel the same level of enjoyment',
					"Betting money you can't afford to lose",
					'Lying about your activities to hide how much you gamble',
					'Using gambling as an escape from everyday life',
					'Neglecting personal or professional responsibilities to gamble',
				],
				'card2Title' => 'What compulsive gambling can cost',
				'card2Items' => [
					'Winning and losing large amounts of money',
					'Relationship and work problems caused by gambling',
					'Guilt or remorse about gambling, yet feeling unable to stop',
					'Stealing or other criminal activity to fund the next bet',
					'Worsening depression, anxiety or substance use alongside the gambling',
					'Neglected physical health, sleep and overall wellbeing',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Cognitive behavioural therapy led by Western-trained therapists sits at the core, alongside mindfulness meditation, exercise therapy and integrated care for co-occurring depression, anxiety or substance use.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site and a low staff-to-client ratio, your recovery program is tailor-made around your history and your gambling, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care when urges hit hardest', 'body' => 'Round-the-clock care from a dedicated therapeutic team means someone is always there when the urge to gamble feels strongest, through the night included.' ],
			],
			'holistic' => [
				'eyebrow' => 'An addiction, not a weakness',
				'heading' => 'A holistic approach to gambling addiction',
				'body' => "Gambling hijacks the same reward system as drugs and alcohol. The rush of euphoria that follows a win comes from a sudden release of dopamine, and over time repeated gambling produces long-lasting changes to the brain's reward pathways, which is why willpower alone rarely holds and why compulsive gambling can be every bit as harmful as substance abuse.\n\nYou'll move through a thorough clinical assessment into daily therapy that works on your triggers, your money behaviours and any underlying depression or anxiety, supported by mindfulness meditation and exercise, all inside a calm, private setting far from every bookmaker and casino.",
			],
			'phases' => [
				'heading' => 'Three pillars of gambling recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Distance, structure and a clear picture',
						'paragraphs' => [
							'Gambling disorders affect everyone differently, so treatment begins with understanding yours.',
							'Our clinical team starts with a comprehensive assessment of the psychological and social factors surrounding your gambling, including any co-occurring depression, anxiety or substance use. At the same time, the residential setting puts real distance between you and every betting app, bookmaker and casino, and a steady early routine of rest and structure lets the compulsion begin to quiet.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'Complete distance from gambling environments and apps',
							'A steadying routine of rest, sleep and structure',
						],
						'asideQuote' => '"The first task is simply to put space between you and the next bet. Once the noise stops, the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Reprogramming the urge to bet',
						'paragraphs' => [
							'Cognitive behavioural therapy sits at the centre of your days here, led by Western-trained behavioural health therapists with extensive experience in CBT for gambling addiction.',
							'Through daily one-to-one sessions and small group work, you will learn to recognise the cognitive distortions that drive the urge to bet, deconstruct the dysfunctional behaviour around money, and build healthy problem-solving skills for coping with stressful situations, while integrated therapy addresses the strain gambling has placed on relationships, family and work.',
						],
						'listItems' => [
							'Daily CBT with Western-trained behavioural therapists',
							'One-to-one sessions and small group work',
							'Work on money behaviours, relationships and family strain',
						],
						'asideQuote' => '"Every compulsive gambler carries beliefs about luck, losses and the next big win. Therapy takes those beliefs apart, one by one."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Recovery holds when mind and body learn new habits together.',
							'Guided mindfulness meditation teaches you to manage the cravings and impulsivity that come with gambling addiction, while structured exercise, from swimming and boxing to cycling and weightlifting, rebuilds the physical health compulsive gambling tends to erode. Before you leave, we design a comprehensive relapse-prevention plan focused on long-term recovery, with practical coping strategies for the months ahead.',
						],
						'listItems' => [
							'Guided mindfulness meditation for cravings and impulsivity',
							'Exercise therapy tailored to your fitness level',
							'A comprehensive relapse-prevention plan before you leave',
						],
						'asideQuote' => '"You leave with more than willpower. You leave with a sharpened set of personal skills and a plan for the day temptation comes looking."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to stop at home means living alongside the same betting apps, bookmakers, casinos and credit that keep the cycle running. A residential retreat removes that access entirely and replaces it with structure: a peaceful daily rhythm of therapy, movement and rest, with round-the-clock care from a dedicated therapeutic team, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider gambling rehab?',
				'paragraphs' => [
					'Gambling addiction is a form of behavioural addiction that affects millions of people around the world. Whereas most people treat gambling as a fun, harmless activity, a person with a gambling disorder feels an uncontrollable urge to gamble continuously, even when they are fully aware that the behaviour is damaging their life. The clearest difference between a recreational gambler and a compulsive one is that the former can usually stick to a loss limit, while the latter feels an overwhelming urge to keep betting regardless of whether they are winning or losing.',
					"The pull is neurological, not a failure of character. Gambling has a profound effect on the brain's reward system: the rush of euphoria that accompanies a win is caused by a sudden release of dopamine, a neurotransmitter that plays an important role in your emotional responses. Over time, repeated gambling produces long-lasting changes to those reward pathways, so the urge to win back losses only grows sharper, and stopping without professional treatment becomes very difficult.",
					"As the addiction escalates, bets grow larger to deliver the same enjoyment, money that can't be afforded is wagered and lost, and the behaviour is hidden behind lies while relationships, work and personal responsibilities suffer. Although gambling addiction is sometimes viewed as a less serious problem than drug or alcohol dependence, pathological gambling can be just as harmful to your health and wellbeing as substance abuse, and it rarely travels alone: depression, anxiety and substance use commonly run alongside it, each feeding the other in a continuous cycle.",
					'Recognising that your gambling has developed from harmless fun into compulsive behaviour takes real honesty, and you do not have to regain control on your own. Overcoming a gambling addiction is difficult, but it is possible, and a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 173, 32, 175, 191, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is inpatient gambling rehab more effective than outpatient treatment?',
					'answer' => 'Inpatient treatment for gambling addiction is generally regarded as more effective than outpatient methods. Living onsite removes you from the distractions and triggers of everyday life, sharply reducing the risk of relapse and allowing you to focus fully on your recovery, while our therapeutic team provides round-the-clock care and keeps a close eye on your progress as you advance through your tailor-made program.',
				],
				[
					'question' => 'Is gambling addiction as serious as drug or alcohol addiction?',
					'answer' => "Yes. Although gambling addiction is sometimes viewed as a less serious problem, pathological gambling can be just as harmful to your health and wellbeing as substance abuse. Gambling has a profound effect on the brain's reward system: the euphoria of a win is driven by a sudden release of dopamine, and over time this produces long-lasting changes to the reward pathways, which makes it very difficult to stop without professional treatment.",
				],
				[
					'question' => 'Can you treat gambling addiction alongside depression or anxiety?',
					'answer' => 'Yes. Gambling addiction is common among people who have a co-occurring mental health issue, such as depression or anxiety, or a substance use disorder, and without expert intervention the symptoms of one disorder will often feed the other. Our clinical team works with you to uncover any underlying issues contributing to the addiction and guides you through an integrated treatment plan carefully designed to heal both simultaneously.',
				],
			],
		],
		1023 => [
			'slug' => 'gaming',
			'programTag' => 'gaming program',
			'hero' => [
				'eyebrow' => 'Gaming addiction treatment · Hua Hin',
				'headline' => 'Reclaim real life from gaming, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating behavioural addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Gaming addiction builds quietly, one session at a time, and because no substances are involved it is easy to dismiss. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of gaming addiction',
				'card1Items' => [
					"You can't stop thinking about gaming",
					'Playing for increasingly long periods of time',
					"You've tried and failed to quit gaming before",
					'Losing interest in hobbies you once enjoyed',
					'Problems at school or work caused by play',
					'Being dishonest with family and friends about how much you game',
				],
				'card2Title' => 'What happens when the game stops',
				'card2Items' => [
					'Restlessness, impatience and irritability',
					'Low mood, sadness and sudden mood swings',
					'Anxiety that builds away from the screen',
					'Loneliness and boredom that feel unbearable',
					'Intense urges to get back to the game',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Cognitive behavioural therapy, mental health counselling and group sessions alongside physical therapy and mindfulness meditation, treating the whole person, not just the habit.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'People game compulsively for very different reasons, so there is no one-size-fits-all treatment here. With only twelve clients on site, your plan starts from the root of your problem, never from a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care at every hour', 'body' => 'A 4:1 staff-to-client ratio and 24/7 support mean someone is always there, through restless evenings and the urge to log back in.' ],
			],
			'holistic' => [
				'eyebrow' => 'More than a habit',
				'heading' => 'A holistic approach to gaming addiction',
				'body' => "Modern games are engineered to hold attention, keeping the brain's reward system engaged with steady achievement, social connection and worlds that can feel easier than this one. That is why willpower alone so rarely works, and why compulsive gaming often masks something else: loneliness, anxiety or a low mood the game has become a way to escape.\n\nYou'll move through a thorough clinical assessment into a structured, screen-free daily routine, a true digital detox, and then into therapy that works on whatever the gaming was escaping from, supported by fitness, mindfulness and real-world activity that rebuilds a life offline.",
			],
			'phases' => [
				'heading' => 'Three pillars of gaming recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A calm, structured start',
						'paragraphs' => [
							'Because people game compulsively for very different reasons, treatment begins with understanding yours.',
							"Our clinical team starts with a comprehensive assessment to get to the root of the problem, then builds a structured, screen-free daily routine, a digital detox that resets your sleep and gives the day a shape that no longer revolves around a game.",
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'A structured, screen-free daily routine',
							'Resetting sleep and a natural daily rhythm',
						],
						'asideQuote' => '"The first days away from the screen are the hardest. Structure and steady support make them bearable, and then surprisingly freeing."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the root of the addiction',
						'paragraphs' => [
							'Compulsive gaming is rarely about the game itself. For some the pull is social connection with players around the world; for others it is escape from depression, anxiety or a life that feels stuck.',
							'Through one-to-one mental health counselling, cognitive behavioural therapy and group sessions, you will work on the underlying causes and learn healthier ways to cope with the problems that led you to compulsive gaming, so the urge loses its power over you.',
						],
						'listItems' => [
							'One-to-one mental health counselling',
							'Cognitive behavioural therapy for urges and triggers',
							'Group sessions with people who understand',
						],
						'asideQuote' => '"Lasting recovery starts when you understand what the game was giving you, and find it in real life instead."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life beyond the screen',
						'paragraphs' => [
							'Recovery holds when the life waiting outside the game is worth logging off for.',
							'Physical therapy, fitness and mindfulness meditation become part of your day, alongside real-world activities that rebuild old interests, and before you leave we design a balanced-technology plan so screens go back to serving your life at home rather than running it.',
						],
						'listItems' => [
							'Fitness, physical therapy and mindfulness meditation',
							'Real-world activities that rebuild old interests',
							'A balanced-technology plan for life at home',
						],
						'asideQuote' => '"The goal is not a life without screens. It is a life where you decide when they are on, and when they are off."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to cut back at home means staying within reach of the rigs, devices and routines that built the habit. Residential treatment puts real physical distance between you and the game and replaces it with structure: regular therapy, daily activity and 24-hour support in a calm coastal setting, so all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider gaming addiction treatment?',
				'paragraphs' => [
					"Video gaming has been around for more than five decades, but online gaming is now more popular than ever, and gaming addiction is a real and growing problem. Because no substances are involved, many people assume it isn't a genuine threat. Yet when gaming takes up a significant portion of someone's time, dependence can develop, the lines between virtual reality and real life can blur, and careers, relationships and mental health begin to suffer.",
					"People become dependent on gaming for very different reasons. Modern games are designed to keep the brain's reward system engaged, and for some players the hook is social, playing and communicating with people throughout the world. Others get drawn in as a form of psychological escapism, using the game to numb depression or anxiety rather than face it.",
					'Left unaddressed, the disorder tends to escalate. Sessions grow longer, other hobbies fall away, and problems surface at school, at work and in relationships, often alongside dishonesty about how much time is really spent playing. In severe cases compulsive gaming shades into internet gambling, putting finances as well as wellbeing at risk, and stopping suddenly without support can bring on low mood, anxiety, irritability and restlessness that make abstaining feel almost impossible.',
					"Recognising that gaming has taken over, in your own life or in someone you love, takes real honesty. Gaming disorders affect adults and teens alike, and you don't have to untangle this on your own. A quiet, confidential conversation with our team in Hua Hin is a good place to start, and we can also advise you on how to approach a family member or friend you're worried about.",
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A clinician reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 32, 207, 202, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Is gaming addiction a real addiction if no substances are involved?',
					'answer' => "Yes. When gaming takes up a significant portion of someone's time, genuine dependence can develop and the lines between virtual reality and real life can blur. Stopping suddenly often brings withdrawal-like symptoms, including low mood, restlessness, irritability, anxiety and loneliness, which is why many people find abstaining almost impossible without structured support.",
				],
				[
					'question' => 'What does gaming addiction treatment at The Diamond involve?',
					'answer' => 'Because people resort to compulsive gaming for very different reasons, there is no one-size-fits-all program. We start by getting to the root of your problem, then build a holistic plan that may include one-to-one mental health counselling, cognitive behavioural therapy, group sessions, a structured detox from gaming, physical therapy and mindfulness meditation, all aimed at lasting change and a minimised risk of relapse.',
				],
				[
					'question' => 'Do you treat gaming addiction in teenagers as well as adults?',
					'answer' => 'Yes. Gaming disorders can affect people of all ages, and we offer gaming addiction treatment for both adults and teens. Our admissions team can also advise you on how to approach a family member or friend you feel could benefit from treatment.',
				],
			],
		],
		1004 => [
			'slug' => 'internet',
			'programTag' => 'digital wellness program',
			'hero' => [
				'eyebrow' => 'Internet addiction treatment · Hua Hin',
				'headline' => 'Take your life back from the screen, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating behavioural addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Many people slip into compulsive internet use without even realising, so the signs are easy to dismiss as habit. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of internet addiction',
				'card1Items' => [
					'Compulsively checking feeds, messages and notifications',
					'Losing hours online with no sense of time passing',
					'Neglected sleep, work, schoolwork or relationships',
					'Being dishonest about how much time you spend online',
					'Procrastination, work avoidance and broken schedules',
					'Repeated failed attempts to cut down on your own',
				],
				'card2Title' => 'What happens when you log off',
				'card2Items' => [
					'Restlessness and irritability away from your devices',
					'Anxiety, fear and loneliness when you cannot get online',
					'Low mood that lifts only when you are back on a screen',
					'Mood swings and feelings of guilt about lost hours',
					'A fear of missing out on what is happening without you',
					'Reaching for the phone without thinking about it',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Counselling and proven therapies including CBT, mindfulness meditation, art, recreation and reality therapy, alongside fitness and physiotherapy that restore the body as well as the mind.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'Internet addiction takes many forms, from gaming and social media to compulsive browsing. With only twelve clients on site, your plan is built around your specific pattern, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care at every hour', 'body' => 'A 4:1 staff-to-client ratio and 24/7 support mean someone is always there, including the restless moments when the pull of the screen feels strongest.' ],
			],
			'holistic' => [
				'eyebrow' => 'A real addiction, not a bad habit',
				'heading' => 'A holistic approach to internet addiction',
				'body' => "Modern platforms are engineered to hold your attention, and compulsive use often masks something deeper: anxiety, loneliness or low mood that feels easier to scroll past than to face. That is why willpower alone so rarely works, and why treatment has to look at the person, not just the screen time.\n\nYour path begins with a thorough clinical assessment, followed by a structured offline routine that resets sleep and gives the days shape again. Daily therapy then works on the drivers underneath the compulsion, while holistic practice rebuilds your attention span, confidence and real-world connection.",
			],
			'phases' => [
				'heading' => 'Three pillars of digital recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A structured offline start',
						'paragraphs' => [
							'Internet addiction takes many forms, from gaming and social media dependency to compulsive information seeking, so treatment begins with understanding yours.',
							'Our clinical team starts with a comprehensive assessment of your usage patterns, triggers and overall health, then builds a calm, structured offline routine. Sleep is often the first casualty of life online, so resetting it, along with regular meals and gentle movement, is an early priority.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'A structured daily routine away from screens',
							'A deliberate reset of sleep, meals and movement',
						],
						'asideQuote' => '"The first days are not about punishing yourself for time lost online. They are about giving the day a shape that does not revolve around a screen."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Working on the why, not just the wifi',
						'paragraphs' => [
							'Evidence-based therapy sits at the centre of your days here, focused on the compulsive patterns that keep pulling you back online.',
							'Through daily one-to-one sessions and small group work, cognitive behavioural therapy helps you recognise the thoughts and urges behind compulsive use, while counselling addresses the underlying issues, such as anxiety, loneliness or low mood, that the internet has been covering over.',
						],
						'listItems' => [
							'Daily one-to-one sessions with experienced therapists',
							'CBT focused on compulsive online patterns',
							'Small group work to rebuild real-world connection',
						],
						'asideQuote' => '"The screen is rarely the whole story. When we treat what a person is scrolling away from, the compulsion starts to lose its grip."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life back online',
						'paragraphs' => [
							'Unlike many addictions, the internet cannot simply be avoided forever, so recovery means learning to live with it on your own terms.',
							'Fitness, mindfulness meditation, art and recreation therapy rebuild your attention span and your enjoyment of the offline world, and before you leave we design a balanced-technology plan for home, with practical rules, relapse-prevention strategies and ongoing support.',
						],
						'listItems' => [
							'Fitness, mindfulness and attention restoration every day',
							'A balanced-technology plan designed before you leave',
							'Ongoing support to manage time online and avoid relapse',
						],
						'asideQuote' => '"Success is not never touching a device again. It is going home able to use the internet as a tool, instead of being used by it."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to cut down at home means fighting the feeds, devices and habits that surround you every waking hour. A residential retreat puts real distance between you and them, and replaces the scrolling with structure: a peaceful daily rhythm of therapy, movement and rest by the coast in Hua Hin, with round-the-clock support, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider internet addiction treatment?',
				'paragraphs' => [
					'Internet addiction is widely understood as an impulse control disorder, and although the condition is still being heavily researched, it clearly affects millions of people across the globe. It takes many forms, including social media dependency, game addiction, compulsive information seeking, net compulsions such as online gambling, trading and compulsive shopping, cyber relationship addiction and cybersex addiction. Teens and young adults face the highest risk, but it can affect people of any age and background.',
					'Part of what makes the internet so hard to put down is that it is built that way. Every refresh, notification and new post offers the promise of something rewarding, and many people describe feeling euphoric only when they are online. Over time the brain learns to reach for the screen automatically, whether to escape responsibilities, soothe anxiety or fill a quiet moment, until the habit no longer feels like a choice at all.',
					'Left untreated, the condition tends to escalate, and the lines between the digital world and reality begin to blur. The costs are real: poor performance at work or school, strained relationships, financial hardship, loss of confidence and social skills, insomnia, back and neck ache, vision problems, poor nutrition and a general erosion of mental health. Internet addiction may not be viewed as seriously as substance abuse, but in the long run its toll on your emotional wellbeing can be just as devastating.',
					'The encouraging news is that it is never too late to turn things around. Residential treatment helps people overcome the disorder regardless of their age or the specific form it takes, and acknowledging the problem is the hardest step. If too much of your life, or the life of someone you love, is disappearing into a screen, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'Our clinical team reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 204, 188, 172, 190, 195, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What types of internet addiction do you treat?',
					'answer' => 'Research suggests there are many types of internet addiction, and our program covers all of them, including social media dependency, game addiction, compulsive information seeking, net compulsions such as online gambling, auction sites, stock trading and compulsive online shopping, cyber relationship addiction and cybersex addiction. Whatever form your compulsive use takes, treatment is shaped around your specific pattern.',
				],
				[
					'question' => 'Will I have to give up the internet completely?',
					'answer' => 'No. Unlike some addictions, the internet is woven into modern work and life, so total abstinence is rarely realistic. The goal of treatment is balance: you will learn how to manage your time online, recognise the urges behind compulsive use and avoid relapse, so you can go home using the internet as a tool rather than being controlled by it.',
				],
				[
					'question' => 'Is internet addiction serious enough to need residential treatment?',
					'answer' => 'It can be. Internet addiction is far from harmless, and because it is rarely diagnosed by a medical professional, many people never seek help. Left untreated for years, the consequences can include poor performance at work or school, strained relationships, financial hardship, mental health problems, insomnia and a loss of confidence and social skills. A residential program creates real distance from your devices and routines, which is often exactly what makes recovery possible.',
				],
			],
		],
		1112 => [
			'slug' => 'sex-addiction',
			'programTag' => 'behavioural program',
			'hero' => [
				'eyebrow' => 'Sex addiction treatment · Hua Hin',
				'headline' => 'Take back control, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating behavioural addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Because sex is a normal, healthy part of human behaviour, the boundary between a healthy sex life and compulsive sexual behaviour isn't always clear, which makes it hard to know when to seek help. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of sex addiction',
				'card1Items' => [
					'An inability to resist sexual urges, even when they interfere with daily life',
					'An excessive preoccupation with sexual thoughts and fantasies',
					'Behaviour that escalates over time, taking riskier or more extreme forms',
					'Attempting to reduce sexual behaviour but being unable to do so',
					'Lying about your behaviour or sexual activities to keep them hidden',
					'Continuing despite knowing the behaviour harms you and those you love',
				],
				'card2Title' => 'What it costs day to day',
				'card2Items' => [
					'Relationships consistently damaged by your sexual behaviour',
					'Feelings of guilt and shame that follow sexual activity',
					'Emotional detachment from partners and the people closest to you',
					'Engaging in unsafe sex or feeling unable to remain faithful to a partner',
					'Repeated failed attempts to stop or cut back on your own',
					'Mounting strain on your career, health and peace of mind',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Cognitive behavioural therapy and one-to-one sex addiction counselling sit at the core, alongside yoga, mindfulness meditation and exercise therapy, with integrated care for any co-occurring mental health or substance use issues.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site and a low staff-to-client ratio, your program is tailor-made around your history and the form your addiction takes, never slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Absolute discretion, constant care', 'body' => 'A private villa in the hills above Hua Hin, a small and tight-knit clinical community, and round-the-clock support from a dedicated therapeutic team, so help is there whenever the urge feels strongest.' ],
			],
			'holistic' => [
				'eyebrow' => 'An addiction, not a moral failing',
				'heading' => 'A holistic approach to sex addiction',
				'body' => "Studies indicate that compulsive sexual behaviour affects the brain's reward system in the same way as substance addiction, which is why willpower alone rarely holds, even when you are fully conscious that the behaviour is harming you and the people you love. Beneath the compulsion there are almost always deeper drivers: unresolved trauma, underlying psychological issues, and the guilt and shame that keep the cycle running in secret.\n\nYour path here begins with a thorough clinical assessment, then moves into daily therapy that works on those underlying factors rather than the symptoms alone, supported by yoga, mindfulness meditation and exercise therapy, all inside a private, judgment-free setting where every aspect of your health is treated together.",
			],
			'phases' => [
				'heading' => 'Three pillars of recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Privacy, distance and a clear picture',
						'paragraphs' => [
							'Sex addiction takes many forms, so treatment begins with understanding yours.',
							'Our clinical team starts with a comprehensive assessment of the psychological issues that lie at the root of your addiction, including any co-occurring mental health or substance use problems. At the same time, the residential setting puts real distance between you and the social circles, routines and circumstances that fuel the behaviour, and a steady early rhythm of rest and structure lets the compulsion begin to quiet.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'Complete distance from familiar triggers and routines',
							'A steadying daily structure of rest, privacy and calm',
						],
						'asideQuote' => '"The first task is simply to step out of the environment that keeps the cycle running. Once there is distance, the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Working on the drivers, not just the behaviour',
						'paragraphs' => [
							'Cognitive behavioural therapy sits at the centre of your days here, delivered one-to-one by highly experienced addiction counsellors.',
							'CBT helps you recognise the destructive thought patterns that influence your behaviour and emotions, while deeper therapeutic work unravels the underlying factors and unresolved trauma that contribute to the addiction. You will build a clear understanding of the personal triggers behind compulsive urges and learn how to exchange unhealthy habits for more constructive alternatives.',
						],
						'listItems' => [
							'Daily one-to-one cognitive behavioural therapy',
							'Trauma work on the underlying drivers where indicated',
							'Practical tools for recognising and managing personal triggers',
						],
						'asideQuote' => '"Compulsive behaviour is rarely about sex itself. Therapy works on what sits underneath, and that is where recovery takes hold."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'A healthier relationship, built to last',
						'paragraphs' => [
							'Unlike substance treatment, the goal here is not total abstinence. It is a healthier relationship with sex and intimacy, with firm personal boundaries that hold after you leave.',
							'Yoga, mindfulness meditation and exercise therapy are woven through your program to reduce stress and rebuild overall wellbeing, and you will learn how to integrate these practices into your daily routine at home. Before you leave, we design a relapse-prevention plan with practical tools for managing compulsive urges in the years ahead.',
						],
						'listItems' => [
							'Work on healthy intimacy and personal boundaries',
							'Yoga, mindfulness meditation and exercise therapy',
							'A relapse-prevention plan and tools for the years ahead',
						],
						'asideQuote' => '"You leave with more than resolve. You leave with boundaries you trust, daily practices that steady you and a plan for the day an urge comes looking."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to stop at home means living alongside the same social circles, routines and circumstances that keep the compulsion running, which is why outpatient attempts so often fail. A residential retreat replaces that exposure with privacy and structure: a peaceful daily rhythm of therapy, movement and rest in your own villa, with round-the-clock care from a dedicated therapeutic team, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider sex addiction treatment?',
				'paragraphs' => [
					'Sex addiction, sometimes referred to as hypersexuality disorder, is a form of behavioural addiction characterised by a repetitive, compulsive urge to engage in sexual activities. People with sex addiction experience persistent sexual thoughts that can manifest in many ways, from compulsive pornography use and masturbation to unsafe sex, an inability to remain faithful to a partner, or behaviour that grows steadily more extreme over time.',
					"The pull is neurological, not a failure of character. Some studies indicate that compulsive sexual behaviour affects the brain's reward system in the same way as substance addiction, which may explain why people feel unable to resist sexual impulses even when they are fully conscious that the behaviour is harmful to themselves and those they love. Mental health authorities do not currently define sex addiction as a diagnosable medical condition, but that should not diminish its seriousness or the devastating impact it can have on a person's relationships, career and health.",
					"Because sex is a normal, healthy and pleasurable part of human behaviour, the boundary between a healthy sex life and compulsion isn't always clear, and many people attempt to reduce their behaviour repeatedly without success. As the addiction deepens, the costs mount: lying to hide sexual activities, feelings of guilt and shame after each episode, emotional detachment from partners, and a consistently negative impact on relationships, work and wellbeing.",
					"Recognising that your behaviour has crossed from healthy into compulsive takes real honesty, and you do not have to regain control on your own. Whatever sex addiction looks like for you, treatment works, and a quiet, confidential conversation with our team is a good place to start.",
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 181, 207, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Does sex addiction treatment mean giving up sex entirely?',
					'answer' => "No. Unlike substance abuse treatment, which focuses on permanently avoiding harmful substances, the goal of sex addiction treatment isn't total abstinence. Our program aims to help you nurture a healthier relationship with sex and intimacy, and to develop practical tools for managing compulsive urges in the years ahead.",
				],
				[
					'question' => 'Is sex addiction a recognised medical condition?',
					'answer' => "Mental health authorities do not currently define sex addiction as a diagnosable medical condition. That should not diminish its seriousness: some studies indicate that compulsive sexual behaviour affects the brain's reward system in the same way as substance addiction, and the impact on a person's relationships, career and health can be devastating. Treatment is effective regardless of how the condition is classified.",
				],
				[
					'question' => 'Can sex addiction be treated alongside other mental health or substance use issues?',
					'answer' => 'Yes. People with sex addiction often have co-occurring disorders such as mental health issues or substance abuse problems, and to minimise the risk of relapse it is crucial that all underlying conditions are treated at the same time. Our team of mental health professionals helps you uncover any psychological issues that play a role in the addiction and monitors your progress as you work through your tailor-made program.',
				],
			],
		],
		953 => [
			'slug' => 'crypto',
			'programTag' => 'trading program',
			'hero' => [
				'eyebrow' => 'Crypto addiction treatment · Hua Hin',
				'headline' => 'Step away from the charts, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating behavioural addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Crypto addiction can develop rapidly, and while the gains and losses keep coming, the signs are easy to miss. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of crypto addiction',
				'card1Items' => [
					'Checking crypto prices compulsively, including through the night',
					'Trading more and taking bigger risks hoping for a higher return',
					'Hiding positions and losses, and borrowing money or selling things to keep trading',
					'Thinking about crypto while doing everything else',
					'Spending a lot of money on crypto, even when losing',
					'Neglecting work, relationships and yourself in favour of trading',
				],
				'card2Title' => 'What happens when you try to stop',
				'card2Items' => [
					'Restlessness and irritability when you cannot check the markets',
					'Anxiety that builds the longer you stay away from the charts',
					'Fear of missing the next big move',
					'Intense urges to check prices or open a trade',
					'Low mood and little interest in anything not related to crypto',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Clinical assessment and proven therapies including CBT, motivational interviewing and psychodynamic work, alongside fitness, mindfulness and family sessions that treat the whole person, not just the trading.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your program is a bespoke combination of therapies built around your situation and what drives your trading, never a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care when urges hit hardest', 'body' => 'Crypto markets run day and night, and so do the urges they create. A 4:1 staff-to-client ratio and 24/7 support mean someone is there in the moments when the pull to trade is strongest.' ],
			],
			'holistic' => [
				'eyebrow' => 'A casino in your pocket',
				'heading' => 'A holistic approach to crypto addiction',
				'body' => "Crypto markets never close, and their volatility works like a casino in your pocket. Every trade releases dopamine and serotonin through the same reward circuitry that drives gambling addiction, which is why an impulse-control disorder can take hold rapidly and why willpower alone so rarely beats it.\n\nYou'll move through a thorough clinical assessment into one-to-one therapy on the urges, the money behaviour and the drivers underneath them, from daily stress and boredom to underlying mental health conditions, supported by fitness and mindfulness inside a calm, private setting far away from the screens.",
			],
			'phases' => [
				'heading' => 'Three pillars of recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Distance from the markets first',
						'paragraphs' => [
							'The market never sleeps, and after months of checking prices around the clock, neither do most of our clients.',
							'Our clinical team begins with a comprehensive assessment to understand your situation and what sits beneath the trading, then builds a structured, screen-free early routine that resets your sleep and puts real distance between you and the charts.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'A structured, screen-free daily routine',
							'Sleep reset after months of round-the-clock markets',
						],
						'asideQuote' => '"You cannot think clearly about trading while you are still inside it. The first step is real distance from the charts."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the root of the urge to trade',
						'paragraphs' => [
							'Crypto addiction is an impulse-control disorder, and the real work is understanding the beliefs and urges that keep you reaching for the next trade.',
							'Through cognitive behavioural therapy you will identify the harmful thought and behaviour patterns behind your trading, while motivational interviewing, psychodynamic therapy, group work and family sessions address your money behaviour and the underlying drivers, from daily stress and boredom to anxiety beneath the surface.',
						],
						'listItems' => [
							'Cognitive behavioural therapy on trading beliefs and urges',
							'Motivational interviewing and psychodynamic therapy',
							'Group therapy and family sessions for firm support',
						],
						'asideQuote' => '"The charts are rarely the whole story. Lasting recovery starts when you understand what the trading was doing for you."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the charts',
						'paragraphs' => [
							'Recovery holds when mind and body are rebuilt together, and when going home does not mean going straight back to the screens.',
							'Fitness and mindfulness become part of your daily rhythm, and before you leave we put practical safeguards around your money and devices and design a balanced-technology plan, so the control you regain here lasts in a world where the markets never close.',
						],
						'listItems' => [
							'Fitness and mindfulness woven into daily life',
							'Practical safeguards around money and devices',
							'A balanced-technology plan for life after treatment',
						],
						'asideQuote' => '"Our job is not just to help you stop trading. It is to teach you how to get well and stay well."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to stop trading at home means the next position is always one tap away, day and night. The market never closes, but distance does what willpower cannot. Residential treatment removes that access entirely and replaces it with structure, therapy and round-the-clock support, so all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider crypto addiction treatment?',
				'paragraphs' => [
					'Cryptocurrency addiction is an impulse-control disorder that causes negative consequences to your finances, personal relationships, work, self-esteem and overall health. Many experts consider it a type of internet addiction, but it clearly carries elements of other addictions too, gambling above all. Not everybody who trades will develop a problem, but the fast-paced nature of crypto makes it a slippery slope, and the addiction can develop rapidly.',
					"What makes crypto so hard to put down is what it does to the brain. Researching and trading release dopamine and serotonin, and in markets this volatile, open around the clock, every dip, rally and almost-win keeps that reward loop firing. Psychological factors such as daily stress, boredom and underlying mental health conditions feed the problem, and being surrounded by other crypto enthusiasts makes it feel normal, which is why the developing signs are so easy to miss.",
					'Over time the trading escalates. Losses lead to bigger risks in the hope of a higher return, then to borrowing money and selling possessions to stay in the market, then to debt, lying and isolation from the people around you. Left untreated, crypto addiction can lead to mental health problems, job loss, legal problems, suicidal tendencies and often other forms of addiction.',
					"Recognising the problem and wanting help is the most important step, and you don't have to take it alone. If you're unsure whether what you're experiencing is an addiction, a quiet, confidential conversation with our team will help you understand your situation, with no obligations on your side.",
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 32, 173, 175, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What causes crypto addiction?',
					'answer' => "Crypto addiction is caused by a combination of biological, psychological and environmental factors. Researching and trading crypto release dopamine and serotonin in the brain, producing pleasant emotions that keep you coming back. Daily stress, boredom and underlying mental health conditions contribute, and being surrounded by other crypto enthusiasts makes you more inclined to keep trading. Together, these make the developing signs of crypto addiction easy to miss.",
				],
				[
					'question' => 'Is crypto addiction the same as gambling addiction?',
					'answer' => 'They are closely related. Crypto addiction is an impulse-control disorder, and many experts consider it a type of internet addiction, but it carries clear elements of other behavioural addictions, gambling in particular: volatile prices, the urge to chase losses and the same reward circuitry in the brain. At The Diamond Rehab Thailand we treat impulse-control disorders with a combination of scientifically proven methods that teach you how to regain full control of your life.',
				],
				[
					'question' => 'How is crypto addiction treated at The Diamond Rehab Thailand?',
					'answer' => 'Treatment is built around evidence-based therapies: cognitive behavioural therapy to identify harmful behaviour patterns and thoughts, motivational interviewing to find the motivation you need, psychodynamic therapy to understand your underlying mental processes, and group therapy and family sessions for firm support on your road to recovery. Most importantly, we teach you how to get well and stay well.',
				],
			],
		],
		4372 => [
			'slug' => 'process-addiction',
			'programTag' => 'behavioural program',
			'hero' => [
				'eyebrow' => 'Process addiction treatment · Hua Hin',
				'headline' => 'Break the cycle of compulsive behaviour, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury rehab. Clinical assessment, evidence-based therapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating behavioural addiction',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Process addictions come in many forms, and sufferers are often good at hiding their habits, so the signs are easy to miss. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Common signs of a process addiction',
				'card1Items' => [
					'Struggling to stop a compulsive behaviour despite trying',
					'Escalating time and intensity spent on the activity',
					'Preoccupation with the behaviour between sessions',
					'Continuing despite serious financial, career or health consequences',
					'Secrecy and denial, hiding the habit from family and colleagues',
					'Growing strain on relationships at home or at work',
				],
				'card2Title' => 'What happens when you try to stop',
				'card2Items' => [
					'Restlessness and irritability when the behaviour is cut off',
					'Anxiety that builds until the urge wins out',
					'A flat, low mood without the activity',
					'Intense urges that override good intentions',
					'Returning to the behaviour despite firm resolutions to stop',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Proven therapies including CBT, one-to-one counselling and group work, alongside fitness, nutrition and mindfulness that rebuild the whole person.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist for your history, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care when urges hit hardest', 'body' => 'A 4:1 staff-to-client ratio and 24/7 medical cover mean someone is always there, through the night and the hardest moments.' ],
			],
			'holistic' => [
				'eyebrow' => 'More than willpower',
				'heading' => 'A holistic approach to process addictions',
				'body' => "A process addiction works on the same reward system a substance does; the dependence forms on the feeling the behaviour provides, no substance required. That is why gambling, gaming, shopping or compulsive internet use can do as much damage to finances, relationships and careers as any drug, and why willpower alone so rarely ends it.\n\nOur program begins with a thorough clinical assessment, then targets the specific behaviour and the drivers underneath it through counselling and behavioural therapy, supported by mindfulness, meditation and exercise inside a calm, private setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Understanding the behaviour first',
						'paragraphs' => [
							'No two process addictions look the same. Treatment starts with a thorough clinical assessment to identify the specific behaviour, how far it has progressed, and any co-occurring mental or physical health issues feeding it.',
							'From day one you step into a calm, structured routine away from the behaviour and its cues, with our team around you, so the compulsion loses its grip while the real work begins.',
						],
						'listItems' => [
							'Comprehensive clinical assessment on arrival',
							'Screening for co-occurring conditions',
							'A structured daily routine away from the behaviour',
						],
						'asideQuote' => '"You cannot treat a behaviour you have not understood. The assessment tells us exactly what we are working with."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Behavioural therapy',
						'h3' => 'Getting to the root of the compulsion',
						'paragraphs' => [
							'A process addiction is rarely about the activity itself. The behaviour becomes a way of coping, and the dependence is on the feeling it provides.',
							'Through one-to-one counselling, group sessions and evidence-based behavioural therapy, you will uncover the drivers beneath the compulsion and learn to recognise the triggers and mental cues that set it off, so they lose their power over you.',
						],
						'listItems' => [
							'One-to-one counselling with experienced therapists',
							'Group sessions and evidence-based behavioural therapy',
							'Understanding your triggers and mental cues',
						],
						'asideQuote' => '"Lasting change starts when you understand what the behaviour has been doing for you, not just what it has been doing to you."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after rehab',
						'paragraphs' => [
							'Recovery holds when mind and body are rebuilt together, and when you leave with a plan rather than just willpower.',
							'Mindfulness, meditation, exercise and wellness therapy become part of your day, and before you go home we design a tailor-made relapse prevention plan, so the behaviour has no easy way back into your life.',
						],
						'listItems' => [
							'Mindfulness, meditation and exercise woven into daily life',
							'Wellness therapy in a peaceful, private setting',
							'A tailor-made relapse prevention plan',
						],
						'asideQuote' => '"Recovery is a life-long journey. Our job is to make sure you leave with the tools to keep the behaviour out of your life for good."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to break a compulsive behaviour at home means staying within easy reach of it, surrounded by the cues, devices and routines that keep the cycle turning. Residential treatment removes that access entirely and replaces it with a peaceful, structured environment and round-the-clock support, so all of your energy goes into recovery.',
			],
			'prose' => [
				'heading' => 'Is it time to consider process addiction treatment?',
				'paragraphs' => [
					'A process addiction, also known as a behavioural addiction, affects people who compulsively engage in a behaviour or activity without being able to stop themselves, despite knowing the potential consequences. The most common forms include gambling, gaming, internet use, shopping, sex and love addiction, exercise addiction, codependency and eating disorders, and together they affect even more people than substance addiction.',
					"For a long time, addiction was thought of as a physical dependency on a substance, and even experts disputed whether behavioural addiction was a real disease. That has changed; it is now widely accepted as a genuine health condition, recognised by many of the world's leading healthcare bodies. The dependence forms not on a drug but on the feeling a behaviour or activity provides, which is why the scientific and therapeutic communities now see little difference between process and substance addiction.",
					'Left untreated, a process addiction tends to escalate. Sufferers are often skilled at hiding their habits, and many families only understand the cause after extreme consequences, such as having to sell a property to cover gambling debts. Strained relationships at home and at work, career problems and mounting mental and physical health issues are common, and these disorders are on the rise as more of daily life moves online.',
					'Acknowledging the problem takes real courage, and any addiction, no matter how challenging it may seem, can be overcome with the right help. If a compulsive behaviour has taken hold of your life or the life of someone you love, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of behavioural therapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 214, 3437, 173, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What types of behaviours can become process addictions?',
					'answer' => 'Process addictions come in many shapes and forms. Common examples include gambling, gaming, internet use, shopping, sex and love addiction, exercise addiction, codependency and eating disorders. Such disorders are on the rise, especially as people spend more time online, so knowing the signs matters; if you are unsure whether a behaviour has crossed the line, our team can help you make sense of it.',
				],
				[
					'question' => 'Is a process addiction as serious as a substance addiction?',
					'answer' => 'Yes. Many people still assume a behavioural addiction has less impact on a person\'s life than a substance addiction, but the scientific and therapeutic communities now see little difference between the two. The dependence forms on the feeling the behaviour provides rather than on a drug, and left untreated the consequences are very similar: damaged finances, strained relationships, career problems and mental and physical health issues.',
				],
				[
					'question' => 'How is process addiction treated at The Diamond Rehab Thailand?',
					'answer' => 'There is no single treatment that works for everyone, so we take the time to understand your situation and build a bespoke, long-term plan. Depending on your needs it may incorporate one-to-one counselling, group sessions, behavioural therapy, mindfulness programs, meditation, exercise and wellness therapy, delivered in a peaceful, private setting in Hua Hin by experienced therapists, psychologists and counselling specialists.',
				],
			],
		],
		1069 => [
			'slug' => 'eating-disorders',
			'programTag' => 'eating disorder program',
			'hero' => [
				'eyebrow' => 'Eating disorder treatment · Hua Hin',
				'headline' => 'Heal your relationship with food, and with yourself, privately in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury retreat. Medical and psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating eating disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Eating disorders rarely announce themselves. They grow quietly, in private rules and routines around food, weight and body shape. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Behavioural and emotional signs',
				'card1Items' => [
					'Preoccupation with food, body weight or body shape',
					"Rigid rules about what, when or how much you're allowed to eat",
					'Secrecy or anxiety around meals',
					'Guilt, embarrassment or distress after eating',
					'A sense of losing control around food',
					'Withdrawing from friends, family and shared meals',
				],
				'card2Title' => 'Physical signs to take seriously',
				'card2Items' => [
					'Marked weight changes in either direction',
					'Dizziness or fainting',
					'Digestive and gastrointestinal problems',
					'Feeling cold when others are comfortable',
					'Low energy and constant fatigue',
					'Dental problems and dehydration',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Medical and psychiatric care, proven psychotherapies including behavioural therapy, and dedicated nutrition support, alongside wellness practices that heal the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist around your history and your eating disorder, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Medical monitoring day and night', 'body' => 'Eating disorders carry real physical risk, so a 4:1 staff-to-client ratio and 24/7 clinical cover mean your health is watched over at every hour, and at every meal.' ],
			],
			'holistic' => [
				'eyebrow' => 'An illness, not a choice',
				'heading' => 'A holistic approach to eating disorders',
				'body' => "An eating disorder is a serious psychological illness, not a choice and not vanity. It takes hold of how a person thinks about food, weight and body shape, and it affects both mind and body, which is why willpower, diets and well-meaning advice so rarely work on their own. Left untreated, eating disorders can lead to serious health problems, which is also why recovery should never be attempted without proper medical support.\n\nYour path here begins with a thorough medical and psychiatric assessment, so any physical complications are identified, treated and monitored from day one. From there, regular psychotherapy and supported nutrition help you understand the thoughts, feelings and behaviours beneath the disorder, while holistic work in a calm, private setting rebuilds strength and self-trust.",
			],
			'phases' => [
				'heading' => 'Three pillars of recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Physical health comes first',
						'paragraphs' => [
							'Eating disorders affect the body as much as the mind, and the first days are about making sure you are physically safe and steady.',
							'Our doctors and psychiatrist begin with a comprehensive medical and psychiatric assessment, identifying and treating any physical complications and prescribing appropriate medication where required. Supported meals and a gentle daily structure follow, so eating becomes calmer and more predictable from the start.',
						],
						'listItems' => [
							'Comprehensive medical and psychiatric assessment on arrival',
							'Physical complications identified, treated and monitored',
							'Supported meals and a steadying daily structure',
						],
						'asideQuote' => '"Physical health comes first. When the body is safe and steady, the real work of recovery can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Getting to the core of the issue',
						'paragraphs' => [
							'For long-lasting wellness, treatment has to reach the core of the issue, not just the eating itself.',
							'In regular one-to-one sessions and small group work, our highly qualified therapists use cognitive behavioural therapy and other evidence-based approaches to work through the behaviours, thoughts and feelings that contribute to your eating disorder, including body image and the deeper drivers beneath it.',
						],
						'listItems' => [
							'Regular one-to-one sessions with experienced therapists',
							'Cognitive behavioural therapy and other evidence-based approaches',
							'Work on body image and the underlying drivers',
						],
						'asideQuote' => '"An eating disorder is never only about food. Therapy works on what the food has come to stand for."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'A relationship with food worth keeping',
						'paragraphs' => [
							'Recovery holds when healthy eating stops being a set of rules and becomes a sustainable way of living.',
							'You will learn to normalise your eating patterns and exchange unhealthy habits for sustainable alternatives, supported by nutrition education, gentle movement and mindfulness. Before you leave, we design a personal aftercare plan so the progress you make here travels home with you.',
						],
						'listItems' => [
							'A sustainable relationship with food and movement',
							'Nutrition education, gentle movement and mindfulness',
							'A personal aftercare plan before you leave',
						],
						'asideQuote' => '"Recovery is not a stricter set of rules. It is a relationship with food, and with yourself, that you can actually live with."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Inpatient care is the most effective way of treating eating disorders, and there are practical reasons why. A residential retreat keeps medical support close at every hour, which matters for an illness with real physical risks. It also puts distance between you and the daily pressures, mirrors-and-scales routines and ingrained patterns that keep the disorder running, and replaces them with consistent, compassionate support at every meal, with your program closely monitored and adjusted as you progress.',
			],
			'prose' => [
				'heading' => 'Is it time to consider eating disorder treatment?',
				'paragraphs' => [
					'An eating disorder is a psychological condition that causes unhealthy eating habits to develop, usually alongside a preoccupation with food, body weight or body shape. The most common types we treat are anorexia nervosa, marked by extreme food restriction and a distorted view of one\'s own body; bulimia nervosa, where episodes of binge eating are followed by compensatory behaviours; binge or overeating disorder, a compulsion to eat large amounts with a sense of lost control, often followed by guilt; and other specified feeding or eating disorders, the diagnosis given when symptoms do not fit neatly into the other categories.',
					'Eating disorders are not about vanity. They are complex illnesses shaped by a combination of biological and environmental factors: genetics, impulsivity, nutritional deficiencies, societal and professional pressure to look a certain way, a history of severe trauma, and low self-esteem all raise the risk. They typically develop during adolescence but can affect people of any age and any gender, and they often occur alongside anxiety, depression or substance use, which is why treatment has to look at the whole person rather than the eating alone.',
					'Early, professional treatment matters because eating disorders take a serious toll on physical health, mental health and quality of life, and left untreated they can lead to serious health problems and may even result in death. That is a reason for care, not for panic: with medical monitoring, psychotherapy and supported nutrition, recovery is genuinely possible, and the sooner treatment begins, the more of that toll can be prevented or reversed.',
					'Eating disorder recovery is challenging, but we want to reassure you that it is possible. You are capable of making great changes, and our team is here to make recovery a safe and comfortable experience from beginning to end. If any of this feels familiar, in your own life or in someone you love, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, supported nutrition and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 3456, 3455, 209, 212, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What is the difference between an eating disorder and disordered eating?',
					'answer' => 'Disordered eating is not a formal clinical diagnosis, and its symptoms are usually less severe and less frequent than those of an eating disorder. An eating disorder is a recognised psychological condition involving unhealthy eating habits and a preoccupation with food, body weight or body shape, and it can seriously affect physical health, mental health and quality of life. If you are unsure where you or a loved one sits on that line, a confidential conversation with a clinician is the safest way to find out.',
				],
				[
					'question' => 'What causes eating disorders?',
					'answer' => 'The exact causes are still unknown, but researchers believe a combination of biological and environmental factors raises the risk. These include genetics, impulsivity, nutritional deficiencies, professions and social pressures that promote thinness, a history of severe trauma or sexual abuse, and low self-esteem. Eating disorders also often occur alongside other mental health conditions such as anxiety, depression and substance use, which is why our treatment addresses the underlying drivers rather than the eating alone.',
				],
				[
					'question' => 'Which eating disorders does The Diamond Rehab Thailand treat?',
					'answer' => 'Our clinicians have extensive experience across the entire spectrum of eating disorders, including anorexia nervosa, bulimia nervosa, binge or overeating disorder, and other specified feeding or eating disorders (OSFED). This is not an exhaustive list: whatever your eating disorder looks like, we will work with you to understand its root cause and build a treatment plan around your unique needs.',
				],
			],
		],
		1082 => [
			'slug' => 'anorexia',
			'programTag' => 'eating disorder program',
			'hero' => [
				'eyebrow' => 'Anorexia treatment · Hua Hin',
				'headline' => 'Come back to yourself, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury retreat. Medical and psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating eating disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Anorexia tends to build quietly, through rules and fears that feel private and can be very hard to see from the inside. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Behavioural and emotional signs',
				'card1Items' => [
					'An intense fear of gaining weight',
					'Constant preoccupation with food, diet and weight',
					'Rigid rules and rituals around eating',
					'A distorted view of your own body',
					'Avoiding meals with other people',
					'A genuine sense that nothing is wrong',
				],
				'card2Title' => 'Physical signs to take seriously',
				'card2Items' => [
					'Significant weight loss',
					'Persistent fatigue and exhaustion',
					'Dizziness',
					'Trouble sleeping',
					'Loss of menstrual periods',
					'Wearing layered clothing to hide your body',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Mind and body, treated together', 'body' => 'Medical and psychiatric care, cognitive behavioural therapy and gently supported nutrition work side by side, because lasting recovery from anorexia means healing both the mind and the body.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your treatment plan is built by our clinical team around your history, your health and your pace, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Medical care day and night', 'body' => 'Fully equipped medical facilities and 24/7 clinical cover mean your physical health is monitored throughout your stay, with kind, steady support at every meal.' ],
			],
			'holistic' => [
				'eyebrow' => 'More than a problem with food',
				'heading' => 'A holistic approach to anorexia',
				'body' => "Anorexia nervosa is a serious mental illness, not a diet gone too far and not a choice. The underlying problem is rarely about food itself: it is an unhealthy way of coping with stress and emotional difficulty, held in place by an intense fear of gaining weight and a distorted view of one's own body. That is why pressure, ultimatums and willpower alone do not work, and why kindness and clinical skill have to arrive together.\n\nAt The Diamond Rehab Thailand, recovery begins with a thorough medical and psychiatric assessment, because physical safety comes first. From there, carefully paced psychotherapy and gently supported nutrition address the fears and the patterns beneath the disorder, while wellness practices, rest and a calm, private setting in the mountains of Hua Hin give your body and mind the room to heal.",
			],
			'phases' => [
				'heading' => 'Three pillars of anorexia recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'Physical safety comes first',
						'paragraphs' => [
							'Anorexia is the most medically serious of the eating disorders, which is why the first days are led by doctors rather than expectations.',
							'Our clinical team begins with a thorough medical and psychiatric assessment, then monitors your physical health closely as your body begins to recover. Meals are gently supported and carefully paced under medical supervision, with rest and an unhurried daily rhythm, and never with pressure.',
						],
						'listItems' => [
							'Thorough medical and psychiatric assessment on arrival',
							'Physical health monitored closely, day and night',
							'Gently supported meals, rest and a calm routine',
						],
						'asideQuote' => '"The first task is not to fix everything. It is to make sure you are physically safe, and to take the pressure off."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Working on what sits beneath',
						'paragraphs' => [
							'Cognitive behavioural therapy forms the core of our anorexia treatment program, because the disorder lives in thoughts and fears as much as in behaviour.',
							'In one-to-one sessions you will work gently on body image, the need for control and the stress and emotional difficulties the disorder has been managing for you, always paced to your physical and emotional capacity rather than a fixed schedule.',
						],
						'listItems' => [
							'One-to-one cognitive behavioural therapy with experienced therapists',
							'Careful work on body image, fear and control',
							'Sessions paced to where you are, never forced',
						],
						'asideQuote' => '"Anorexia is rarely about food itself. Therapy works on the fear and the need for control underneath, at a pace the person can bear."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for the years ahead',
						'paragraphs' => [
							'Recovery holds when trust with food and your own body has been rebuilt, and when daily life supports it.',
							'Wellness practices, movement and rest become part of a sustainable routine, and before you leave we design a personal aftercare plan with you, so you graduate feeling strong, healthy and able to take ownership of your eating habits in the years ahead.',
						],
						'listItems' => [
							'Wellness practices that rebuild trust in your body',
							'Sustainable daily routines, developed with our team',
							'A personal aftercare plan before you leave',
						],
						'asideQuote' => '"You should leave feeling strong and healthy, with habits that keep working long after you go home."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover from anorexia at home means doing it inside the same pressures, routines and triggers that helped the disorder take hold. A residential stay offers medical safety around the clock, consistent and compassionate support at every meal, and real distance from daily life, so all of your energy can go into healing. Residential treatment for anorexia is generally regarded as more effective than outpatient therapy for exactly this reason.',
			],
			'prose' => [
				'heading' => 'Is it time to consider anorexia treatment?',
				'paragraphs' => [
					'Anorexia nervosa, more commonly known simply as anorexia, is a serious eating disorder in which a person severely restricts how much they eat, driven by an intense fear of gaining weight and a distorted view of their own body. Although young women are disproportionately affected, anyone of any age, gender or cultural background can develop anorexia, and a related condition, atypical anorexia nervosa, shares almost all of its features in people whose weight appears unremarkable from the outside.',
					'Although anorexia shows itself through eating, the underlying problem is rarely about food. It is a mental health condition: an unhealthy way of coping with stress and emotional difficulty, often bound up with a need for control. Because the disorder distorts how a person sees their own body and behaviour, it can be genuinely difficult to recognise from the inside, and many people do not realise how unwell they have become.',
					'Anorexia is among the most serious mental illnesses, and without effective treatment it takes a heavy toll on both physical and psychological health, which is why medical supervision matters. The encouraging news is that recovery is real: research suggests that about 46 percent of people with anorexia make a full recovery and a further 33 percent make a partial one, and the sooner treatment begins, the better the outlook.',
					'Reaching out takes courage, especially when part of you is not sure anything is wrong. You do not have to be certain, and you do not have to do it alone. Whether you are worried about yourself or recognise these signs in someone you love, a quiet, confidential conversation with our eating disorder team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, supported nutrition and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 32, 200, 212, 214, 206, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'What is the recovery rate for anorexia?',
					'answer' => 'Research suggests that about 46 percent of people with anorexia make a full recovery and an additional 33 percent make a partial recovery. The sooner a person gets treatment for anorexia nervosa, the more likely they are to make a full recovery, so it is important to seek help as early as possible. Residential treatment for anorexia is generally regarded as more effective than outpatient therapy.',
				],
				[
					'question' => 'Can you have anorexia and not realise it?',
					'answer' => 'Yes. Anorexia can cause severe body image distortion and dramatically affect the way you think and feel about yourself and the world around you, which makes it very difficult to self-diagnose. Generally speaking, if you have a persistent fear of gaining weight or find yourself going to unhealthy lengths to lose it, it is worth talking to a professional. For a formal diagnosis, please see a doctor or another licensed professional.',
				],
				[
					'question' => 'What is atypical anorexia nervosa?',
					'answer' => 'Atypical anorexia nervosa is a serious eating disorder that shares almost all of the features of anorexia nervosa. The difference is that a person with atypical anorexia nervosa is not visibly underweight: their weight falls within or above the typical range. It can still cause serious malnutrition, deserves the same careful medical attention, and may progress into anorexia nervosa without treatment.',
				],
			],
		],
		1089 => [
			'slug' => 'bulimia',
			'programTag' => 'eating disorder program',
			'hero' => [
				'eyebrow' => 'Bulimia treatment · Hua Hin',
				'headline' => 'Break the cycle and make peace with food, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury retreat. Medical and psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating eating disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Bulimia is often invisible. Weight can fluctuate or look entirely normal, and most people work hard to keep the illness hidden, so the signs are easy to miss. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Behavioural and emotional signs',
				'card1Items' => [
					'Cycles of bingeing followed by compensating, kept secret',
					'Secrecy and unease around food and mealtimes',
					'Disappearing to the bathroom soon after eating',
					'Preoccupation with body image and weight',
					'Depression, mood swings and harsh self-criticism',
					'Wearing loose clothing to hide the body',
				],
				'card2Title' => 'Physical signs to take seriously',
				'card2Items' => [
					'Swollen glands in the neck or face',
					'Dental erosion and a frequently sore throat',
					'Heartburn, indigestion and bloating',
					'Dehydration',
					'Weakness, fatigue and exhaustion',
					'Irregular periods',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Medical and psychiatric care with cognitive behavioural therapy, the most strongly evidenced psychotherapy for bulimia, alongside supported nutrition, mindfulness meditation and gentle movement that steady the whole person, not just the symptoms.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist around your history and your relationship with food, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Medical monitoring day and night', 'body' => 'A 4:1 staff-to-client ratio and 24/7 clinical cover mean someone is always there, including in the difficult moments after meals when the cycle pulls hardest.' ],
			],
			'holistic' => [
				'eyebrow' => 'An illness, not a lack of discipline',
				'heading' => 'A holistic approach to bulimia',
				'body' => "Bulimia nervosa is a serious, recognised mental illness, not a failure of willpower. It runs on a self-reinforcing cycle: distress about body image drives bingeing, bingeing drives compensating, and each episode deepens the shame that triggers the next. That shame is also what keeps the illness hidden, so treatment here begins by lifting it. Nobody at the retreat will judge you, and nothing you share will surprise us.\n\nYour path starts with a thorough medical and psychiatric assessment, then moves into daily psychotherapy focused on the cycle and what drives it, supported by regular, gently structured meals and nutrition education. Around that clinical core, mindfulness, movement and rest in a calm, private setting give your body and mind the space to heal together.",
			],
			'phases' => [
				'heading' => 'Three pillars of bulimia recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A safe, judgement-free start',
						'paragraphs' => [
							'Bulimia affects the body as well as the mind, so treatment begins with a complete and careful picture of both.',
							'Our doctors and psychiatrist carry out thorough medical and psychiatric assessments, including physical health checks on the heart, hydration, digestion and dental health that bulimia can quietly strain. From there, regular supported meals and a steady daily routine begin to settle the cycle, with any medication reviewed by the psychiatrist where appropriate.',
						],
						'listItems' => [
							'Medical and psychiatric assessment on arrival',
							'Careful physical health checks and ongoing monitoring',
							'Regular supported meals to steady the cycle',
						],
						'asideQuote' => '"The first step is never judgement. It is making sure you are physically well, and showing you that the cycle can be interrupted."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Understanding the cycle, and what drives it',
						'paragraphs' => [
							'The most up-to-date research points to cognitive behavioural therapy as the most effective psychotherapy for bulimia nervosa, and it sits at the centre of your days here.',
							'Through daily one-to-one sessions and small group work, you will learn how the binge-and-compensate cycle sustains itself, gently challenge the thoughts about body image and self-worth that feed it, and build steadier ways of responding to the emotional triggers behind it.',
						],
						'listItems' => [
							'Cognitive behavioural therapy, the gold standard for bulimia',
							'Daily one-to-one sessions and small group work',
							'Work on body image, self-worth and emotional triggers',
						],
						'asideQuote' => '"Bulimia convinces people that they are the problem. Therapy shows them the cycle is the problem, and that the cycle can be unlearned."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Lasting recovery means leaving with routines that hold once the structure of the retreat is gone.',
							'Nutrition education, mindfulness meditation and gentle daily movement help you rebuild a calm, sustainable relationship with food and self-care. Before you leave, we design a personal relapse-prevention and aftercare plan, with ongoing support arranged for the months after you return home.',
						],
						'listItems' => [
							'Nutrition education and sustainable self-care routines',
							'Mindfulness meditation and gentle daily movement',
							'A personal relapse-prevention and aftercare plan',
						],
						'asideQuote' => '"Leaving is not the end of treatment. You go home with a plan, with practical routines, and with people who still answer when you call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'At home, bulimia leans on privacy and routine: the same kitchens, bathrooms and quiet hours where the cycle has always lived. A residential stay replaces that with gentle structure around every meal, real distance from the private routines the illness depends on, and someone beside you in the hardest moments after eating, so the urge to compensate can pass with support rather than in secrecy.',
			],
			'prose' => [
				'heading' => 'Is it time to consider bulimia treatment?',
				'paragraphs' => [
					'Bulimia nervosa is one of the three main eating disorders, and it is both a mental illness and an eating disorder. It is defined by recurrent episodes of binge eating, consuming far more food in a short window than most people would, followed by compensating behaviours intended to undo the episode. Because weight tends to fluctuate rather than fall, bulimia is often harder to identify than eating disorders like anorexia, and many people who live with it are in denial or become skilled at hiding their habits, which is why it so often goes unrecognised even by those closest to them.',
					"The cycle is usually driven by worry about body image in a world that constantly tells us how we should look. One study suggested that up to 40% of women have dieted to lose weight despite not being overweight in the first place, and research shows women and young adults face the highest risk of developing bulimia. Genetics, mental health and personality factors can all play a part. What matters most to understand is that the cycle is self-reinforcing: each episode deepens the distress and self-criticism that trigger the next, which is why willpower alone so rarely breaks it.",
					'Professional treatment works because it addresses the underlying problem rather than the behaviour alone. Current research points to cognitive behavioural therapy as the most effective psychotherapy for bulimia nervosa, and the strongest programs combine it with counselling, support groups, nutrition education, mindfulness meditation and medication where appropriate. Left untreated, however, bulimia carries serious physical risks: cardiac complications ranging from an irregular heartbeat to heart failure, dehydration and the loss of essential minerals that keep the organs functioning, digestive problems, pancreatitis and ulcers, dental damage, and deep fatigue. In severe cases these complications can be life-threatening, which is why early treatment matters so much.',
					"Reaching out takes real courage, especially with an illness that grows in secrecy and shame. You will not be judged here, and nothing you tell us will shock us. If the cycle has taken hold of your life, or the life of someone you love, a quiet, confidential conversation with our team is a good place to start.",
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, supported nutrition and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 204, 213, 182, 212, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'How long does bulimia treatment take?',
					'answer' => 'It depends on the person and the severity of the illness. A residential stay typically lasts between four and eight weeks, after which regular ongoing support is encouraged to protect recovery. For some people, maintaining a healthy relationship with food is a longer endeavour, which is why a personal relapse-prevention and aftercare plan is built into every program before you leave.',
				],
				[
					'question' => 'Can you recover from bulimia?',
					'answer' => 'Yes. Recent studies suggest that up to 50% of women recover from bulimia nervosa within ten years of being diagnosed, and structured professional treatment meaningfully improves those chances. Because around 30% of people who recover will experience a relapse at some point, good treatment always pairs therapy with a relapse-prevention and aftercare plan, so a difficult week never has to undo your progress.',
				],
				[
					'question' => 'What is the best treatment for bulimia nervosa?',
					'answer' => 'The most up-to-date research suggests that cognitive behavioural therapy (CBT) is the most effective form of psychotherapy for bulimia nervosa. The strongest programs combine CBT with counselling, support groups, nutrition education, mindfulness meditation and, where appropriate, medication, within a structured daily routine that helps steady the cycle while the deeper work takes hold.',
				],
			],
		],
		1094 => [
			'slug' => 'overeating',
			'programTag' => 'eating disorder program',
			'hero' => [
				'eyebrow' => 'Binge eating treatment · Hua Hin',
				'headline' => 'Make peace with food, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury retreat. Medical and psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating eating disorders',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Almost everyone overindulges from time to time, so binge eating disorder is easy to dismiss as a habit. It is not. It tends to live in private routines and quiet shame, which is why it goes unrecognised for so long. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Behavioural and emotional signs',
				'card1Items' => [
					'Eating large amounts rapidly, or well past the point of comfortable fullness',
					'Eating alone or in secret out of embarrassment',
					'Feeling guilty, ashamed or low after eating',
					'Reaching for food to cope with stress or difficult emotions',
					'Eating even when you are not physically hungry',
					'Diets and resolutions that never seem to hold',
				],
				'card2Title' => 'How it feels day to day',
				'card2Items' => [
					'A sense of having little or no control around food',
					'Thoughts that circle constantly around eating and your body',
					'Low self-worth and a harsh inner voice',
					'Mood swings, anxiety or a persistent low mood',
					'Energy crashes, disturbed sleep and trouble concentrating',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Medical and psychiatric care with proven psychotherapy, led by highly experienced eating disorder specialists, alongside nutrition education, mindfulness, yoga, meditation and massage that steady the whole person, not just the eating.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'Compulsive overeating affects everyone differently. With only twelve clients on site, your plan is built around your history, your triggers and your recovery goals, never slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care at the hard moments', 'body' => 'A 4:1 staff-to-client ratio and 24/7 support from a compassionate team mean someone is always there, including at the times of day when eating feels hardest.' ],
			],
			'holistic' => [
				'eyebrow' => 'An illness, not a weakness',
				'heading' => 'A holistic approach to binge eating',
				'body' => "Binge eating disorder is the most common eating disorder and the least talked about, and it is an illness, not a lack of willpower. Diets do not treat it, because the food is the symptom rather than the cause: for many people, eating becomes an escape or a way to cope with feelings that have nowhere else to go, and the guilt that follows quietly sets up the next episode.\n\nTreatment here works differently. It begins with a thorough medical and psychiatric assessment, then moves into evidence-based psychotherapy that addresses the emotional drivers of eating, supported by regular, unpressured meals and holistic practice in a calm, private setting where judgment has absolutely no place.",
			],
			'phases' => [
				'heading' => 'Three pillars of recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A gentle, judgment-free start',
						'paragraphs' => [
							'Compulsive overeating affects everyone differently, so treatment begins with taking the time to understand how it lives in your life.',
							'Our doctors complete a comprehensive medical and psychiatric assessment, then build a steadying early routine of regular, supported meals, rest and structure. There are no diets here and no rules to fail at, only a calm rhythm that takes the daily negotiation with food off your shoulders.',
						],
						'listItems' => [
							'Comprehensive medical and psychiatric assessment on arrival',
							'Regular, supported meals with no diets and no judgment',
							'A steadying routine of rest, sleep and structure',
						],
						'asideQuote' => '"The first task is not to fix your eating overnight. It is to make food feel ordinary again, so the real work can begin."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Working on what drives the eating',
						'paragraphs' => [
							'Evidence-based psychotherapy sits at the centre of your days here, led by specialists in the field of eating disorders.',
							'Through ongoing cognitive behavioural therapy, in one-to-one sessions and small group work, you will learn to identify the emotions and triggers behind binge episodes, restructure the unhelpful thought patterns that keep them running, and rebuild the self-worth the disorder has worn away.',
						],
						'listItems' => [
							'Ongoing cognitive behavioural therapy with eating disorder specialists',
							'One-to-one sessions and small group work in a safe, judgment-free space',
							'Practical tools for the emotions and triggers behind episodes',
						],
						'asideQuote' => '"Binge eating is rarely about hunger. When the feelings underneath are treated, food begins to lose its grip."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Food is a necessary part of life, so avoidance can never be the goal. The aim is a sustainable, peaceful relationship with eating that holds long after you leave.',
							'Nutrition education, mindfulness, yoga and gentle movement become part of your daily rhythm, and before you go home we design a fully customised relapse prevention and aftercare plan for the triggers of daily life, so the habits you build here travel with you.',
						],
						'listItems' => [
							'Nutrition education for sustainable, informed eating',
							'Mindfulness, movement and self-care woven into every day',
							'A fully customised relapse prevention and aftercare plan',
						],
						'asideQuote' => '"Leaving is not the end of treatment. You go home with practical habits, a plan for the hard days, and people who still answer when you call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover at home means facing the same stressors, routines and private eating patterns that keep the cycle running. A residential retreat puts real distance between you and that environment and replaces it with structure: regular, supported meals, a peaceful daily rhythm of therapy, movement and rest, and round-the-clock care from a compassionate team, so the hardest moments are never faced alone, and never met with judgment.',
			],
			'prose' => [
				'heading' => 'Is it time to consider binge eating treatment?',
				'paragraphs' => [
					'Compulsive overeating, sometimes referred to as binge eating disorder, is a condition characterised by uncontrolled episodes of overeating. Whether it is the holiday season or a celebration, almost everyone overindulges from time to time; this is different. It looks like eating large amounts of food even when you are not hungry, eating until you feel uncomfortably full, eating in secrecy, and feeling embarrassed, guilty or ashamed of your eating afterwards. It typically begins in adolescence or young adulthood, though it can affect people of any age, and it is an illness, not gluttony and not a failure of willpower.',
					'For many people, food becomes an escape or a coping mechanism. Researchers believe a combination of biological, psychological and social factors contributes to the disorder, and studies point to a close relationship with anxiety: some people overeat to temporarily relieve anxious feelings until it becomes an automatic response, and the guilt and shame that follow quietly set up the next episode. Over time the condition wears on the whole person, affecting sleep, energy, focus, body image and mood.',
					'This is also why diets so rarely help. Unlike other compulsions, total abstinence cannot be the goal, because food is a necessary part of life; the challenge lies in learning to appreciate food as nourishment rather than a coping mechanism for times of stress or difficulty. Professional treatment works on the cause rather than the symptom: cognitive behavioural therapy helps you cope with the thoughts and feelings that trigger binge episodes, medication may be considered in moderate to severe cases under careful medical supervision, and nutrition education builds sustainable positive habits. A structured residential program brings all of this together in one calm place.',
					'Committing to treatment takes courage, and judgment has absolutely no place in the healing process. If your relationship with food has become a source of distress, or you are worried about someone you love, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, supported nutrition and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 218, 173, 32, 198, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'How is compulsive overeating treated?',
					'answer' => 'Compulsive overeating disorder is typically treated with a combination of approaches. Cognitive behavioural therapy helps you learn to better cope with the thoughts and feelings that trigger binge eating episodes, and in moderate to severe cases medication may be considered under careful medical supervision. Treatment is not a diet program: the focus is on the emotional drivers of eating and on building a sustainable, healthier relationship with food.',
				],
				[
					'question' => 'Is overeating a sign of anxiety?',
					'answer' => 'Research suggests a complicated relationship between anxiety and overeating. A number of studies suggest that some people compulsively overeat as a way to temporarily relieve feelings of anxiety, which can develop into an automatic response pattern, while other studies have found that people with compulsive overeating disorder are more likely to experience anxiety symptoms than the general population. Effective treatment therefore addresses both the eating and the feelings beneath it.',
				],
				[
					'question' => 'Is binge eating disorder a real illness?',
					'answer' => 'Yes. Compulsive overeating is a recognised condition in which people feel intense reward from eating and have little or no control over their eating habits, despite the effect on their lives. Naming it this way is not a judgment: it confirms that binge eating disorder is a genuine illness with effective, evidence-based treatment, not a question of willpower or character.',
				],
			],
		],
		3679 => [
			'slug' => 'mental-health',
			'programTag' => 'mental health program',
			'hero' => [
				'eyebrow' => 'Mental health treatment · Hua Hin',
				'headline' => 'Get genuinely well again, privately, in Thailand',
				'lede' => "A discreet, doctor-led residential program at Thailand's leading luxury retreat. Psychiatric assessment, evidence-based psychotherapy and a hard cap of twelve clients, so recovery is built around you and never a template.",
				'stat3Label' => 'Years treating mental health',
			],
			'signs' => [
				'heading' => 'Recognising the signs is the first step',
				'subheading' => "Mental illness rarely announces itself all at once. Whether it is depression, anxiety, trauma, burnout or insomnia, the signs surface in mood, body and behaviour. If several feel familiar, it's worth a conversation.",
				'card1Title' => 'Emotional and mental signs',
				'card1Items' => [
					'Persistent low mood that will not lift',
					'Extreme feelings of fear or worry, out of proportion to events',
					'Loss of interest in things you used to enjoy',
					'Withdrawing from family, friends and social life',
					'Feeling overwhelmed and unable to cope with daily challenges',
					'A creeping sense of hopelessness about getting better',
				],
				'card2Title' => 'Physical and behavioural signs',
				'card2Items' => [
					'Poor sleep, whether falling asleep, staying asleep or both',
					'Decreased energy and physical and emotional exhaustion',
					'Changes in appetite and unusual eating behaviour',
					'Reduced mental clarity and trouble concentrating',
					'Relying on alcohol, drugs or other crutches to cope',
					'Work, home and personal obligations starting to slip',
				],
			],
			'pillars' => [
				[ 'num' => '01 · Evidence-based & holistic', 'title' => 'Western clinical care, Eastern calm', 'body' => 'Psychiatric care and proven psychotherapies including CBT and DBT, alongside yoga, meditation, fitness and nutrition that treat the whole person, not just the diagnosis.' ],
				[ 'num' => '02 · Never templated', 'title' => 'A program shaped around you', 'body' => 'With only twelve clients on site, your plan is built by a psychiatrist around your diagnosis and your history, not slotted into a fixed curriculum.' ],
				[ 'num' => '03 · Support around the clock', 'title' => 'Care on the hardest days', 'body' => 'Our staff always outnumber our clients, and 24/7 clinical cover means someone is always there, through the night and the most difficult moments.' ],
			],
			'holistic' => [
				'eyebrow' => 'Common, and treatable',
				'heading' => 'A holistic approach to mental health',
				'body' => "Mental disorders are conditions that affect thinking, emotion and behaviour, causing real distress and wearing away at quality of life. They are far more common than most people admit, and they are treatable. Pushing through alone rarely works, because the illness itself erodes the energy and clarity that recovery demands.\n\nYou'll move through a thorough psychiatric assessment, often the first real diagnosis someone gets, into daily psychotherapy, one-to-one and in small groups, supported by yoga, meditation, fitness and nutrition, all inside a calm, private coastal setting designed to make the work possible.",
			],
			'phases' => [
				'heading' => 'Three pillars of mental health recovery',
				'items' => [
					[
						'phase' => 'PHASE 01',
						'label' => 'Assessment & stabilisation',
						'h3' => 'A careful, accurate start',
						'paragraphs' => [
							'Depression, anxiety, trauma, burnout and insomnia each call for different treatment, so everything begins with understanding exactly what you are dealing with.',
							'Our psychiatrist conducts a comprehensive psychiatric assessment to reach an accurate diagnosis, including any co-occurring conditions, then builds a gentle early routine of rest, sleep and structure. Where appropriate, any medication is reviewed and adjusted by the psychiatrist, always as a support to therapy rather than the whole answer.',
						],
						'listItems' => [
							'Comprehensive psychiatric assessment and diagnosis',
							'A steadying routine of rest, sleep and structure',
							'Medication review by a psychiatrist where appropriate',
						],
						'asideQuote' => '"The right treatment starts with the right diagnosis. For many of our clients, this assessment is the first time anyone has looked at the whole picture."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 02',
						'label' => 'Psychotherapy',
						'h3' => 'Getting to the root causes',
						'paragraphs' => [
							'Evidence-based psychotherapy sits at the centre of your days here, tailored to your diagnosis rather than a standard script.',
							'Through daily one-to-one sessions, and small group work where it helps, you will get beneath the symptoms to the root causes of the disorder, using cognitive behavioural therapy, DBT and trauma-focused work as your diagnosis calls for it.',
						],
						'listItems' => [
							'Daily one-to-one sessions with experienced therapists',
							'CBT, DBT and trauma-focused therapy as needed',
							'Small group work, never mandatory',
						],
						'asideQuote' => '"We don\'t just treat the disorder. We work on the root causes beneath it, because that is what makes recovery last."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
					[
						'phase' => 'PHASE 03',
						'label' => 'Holistic & aftercare',
						'h3' => 'Built for life after the retreat',
						'paragraphs' => [
							'Lasting well-being is the ultimate goal, and it holds when mind and body learn to settle together.',
							'Yoga, meditation, fitness, nutrition and time outdoors become part of your daily rhythm, family therapy is welcomed where it strengthens your support at home, and before you leave we design a personal aftercare plan with practical coping skills for the months ahead.',
						],
						'listItems' => [
							'Yoga, meditation, fitness and nutrition every day',
							'Family therapy to strengthen support at home',
							'A personal aftercare plan before you leave',
						],
						'asideQuote' => '"Leaving is not the end of treatment. You go home with the coping skills and mindset to handle daily challenges, and with people who still answer when you call."',
						'asideMetaLabel' => 'Statement',
						'asideMetaValue' => 'The Diamond Rehab Team',
					],
				],
			],
			'inpatient' => [
				'body' => 'Trying to recover at home means staying inside the environment and obligations that feed the illness. A residential retreat puts real distance between you and those stressors, distractions and triggers, without the feel of a hospital or psychiatric unit, and replaces them with structure: a peaceful daily rhythm of therapy, movement and rest, with round-the-clock support from dedicated mental health professionals, so all of your energy goes into getting well.',
			],
			'prose' => [
				'heading' => 'Is it time to consider mental health treatment?',
				'paragraphs' => [
					'Residential mental health treatment is intensive, doctor-led care delivered in a comfortable retreat setting rather than a hospital or psychiatric unit. At The Diamond Rehab Thailand it covers the full range of common conditions: depression, with its persistent low moods and loss of interest; anxiety disorders, marked by extreme fear or worry; PTSD and trauma, where the emotional response to a disturbing event overwhelms normal coping capacity; burnout, the physical and emotional exhaustion caused by excessive stress; and insomnia, where poor sleep erodes daily functioning.',
					'Everyone has rough patches, so it can be hard to know when difficulty becomes illness. The line is distress and deterioration: a mental health condition causes significant suffering, interferes with work, relationships and daily functioning, and wears down quality of life rather than passing on its own. Low mood that will not lift, worry that never switches off, exhaustion that rest does not fix, or sleep that has fallen apart for weeks are all signals worth taking seriously.',
					'Structured residential treatment works for three reasons. The first is intensity: therapy happens every day rather than one hour a week, in controlled surroundings free of the stressors and triggers of daily life. The second is diagnosis: a comprehensive psychiatric assessment identifies what is actually going on, including dual diagnosis, since research shows that almost one in three people with a formal mental illness diagnosis also have a substance use disorder, and the two must be treated together. The third is continuity: 24-hour care from a team that outnumbers its clients, keeping a close eye on your progress so you are always on the right path.',
					'Acknowledging that something is wrong takes real courage, and you do not have to find the way back on your own. If your mental health, or the mental health of someone you love, has been slipping, a quiet, confidential conversation with our team is a good place to start.',
				],
			],
			'steps' => [
				[ 'label' => 'STEP 01', 'title' => 'Confidential call', 'body' => "A free, no-obligation conversation with our admissions team, whenever you're ready." ],
				[ 'label' => 'STEP 02', 'title' => 'Clinical assessment', 'body' => 'A psychiatrist reviews your situation and recommends the right length of stay.' ],
				[ 'label' => 'STEP 03', 'title' => 'Arrival & onboarding', 'body' => 'We arrange airport collection and settle you into private accommodation.' ],
				[ 'label' => 'STEP 04', 'title' => 'Treatment begins', 'body' => 'A bespoke program of psychotherapy, holistic practice and rest, adjusted as you progress.' ],
			],
			'faqCptIds' => [ 189, 196, 207, 13219, 13220, 13221 ],
			'faqNew' => [
				[
					'question' => 'Which mental health conditions do you treat?',
					'answer' => 'We treat the full range of common mental health conditions, including depression, anxiety disorders, PTSD and trauma, burnout and insomnia, as well as behavioural addictions, impulse control disorders and eating disorders. Because conditions often overlap, treatment starts with a comprehensive psychiatric assessment so your program addresses everything that is actually going on, not just the most visible symptom.',
				],
				[
					'question' => 'What is dual diagnosis and do you treat it?',
					'answer' => 'Dual diagnosis means a mental health disorder and a substance use problem exist side by side. Research shows that almost one in three people with a formal mental illness diagnosis also have a substance use disorder, and the two feed each other when treated separately. We use an integrated approach that explores the relationship between them and treats both within a single rehabilitation plan designed for long-term recovery.',
				],
				[
					'question' => 'Do I have to take part in group sessions?',
					'answer' => 'No. Group meetings are not mandatory at our retreat. Treatment is built around daily one-to-one sessions with your therapists, and we maintain the highest standards of privacy and confidentiality throughout your stay. Small group work is available where it genuinely helps, but how much of it you do is always your choice.',
				],
			],
		],
	];
}
