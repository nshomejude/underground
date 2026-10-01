<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Editorial copy for public detail pages
|--------------------------------------------------------------------------
|
| Long-form, presentational copy keyed by the slug of the capability, sector
| or engagement model it belongs to. The catalogue records (title, summary,
| icon) stay in the database and remain editable from the admin; this file
| only adds the depth a detail page needs. Any slug without an entry here
| falls back to a short, generic treatment in the views.
|
*/

return [

    'capabilities' => [

        'government-political-affairs' => [
            'overview' => [
                'Policy is made by people, in rooms, on timelines that rarely match the public calendar. Our Government & Political Affairs practice gives clients a principled, lawful and discreet way into those rooms: the right officials, the right institutions, at the right moment.',
                'We combine long-standing relationships with a rigorous reading of how a decision will actually be made. That means mapping who holds authority, who holds influence, and what each of them needs to be able to say yes.',
            ],
            'services' => [
                'Stakeholder mapping' => 'A living map of the officials, institutions and informal influencers who shape a decision, and how they relate to one another.',
                'Policy and regulatory positioning' => 'Early, constructive engagement on emerging legislation, licensing regimes and public-investment frameworks.',
                'Institutional introductions' => 'Credible, appropriate access to ministries, agencies, legislatures and regional bodies through established relationships.',
                'Executive briefing and preparation' => 'Preparing principals for high-stakes meetings with clear objectives, context and protocol guidance.',
                'Coalition building' => 'Aligning industry groups, civil society and public institutions behind a shared position.',
                'Ongoing political monitoring' => 'Continuous tracking of political developments that change the terms of an engagement.',
            ],
            'approach' => [
                'Listen' => 'We begin with the decision you need, not the introduction you want.',
                'Map' => 'We chart authority, influence and incentives around that decision.',
                'Engage' => 'We open the right doors in the right order, always within the law and the client’s values.',
                'Sustain' => 'We keep the relationship warm and the context current long after the first meeting.',
            ],
            'outcomes' => [
                'Faster, better-informed access to decision-makers',
                'Policy positions shaped before they harden',
                'Fewer surprises from political and regulatory change',
                'Relationships that outlast a single transaction',
            ],
            'sectors' => ['government-public-sector', 'energy-natural-resources', 'infrastructure-transportation'],
            'engagement' => ['strategic-advisory-retainers', 'government-affairs-lobbying'],
        ],

        'international-relations-diplomacy' => [
            'overview' => [
                'Bilateral and multilateral relationships are rarely linear. Alliances shift, sanctions regimes evolve and emerging markets rewrite the rules of access. We help clients move through that terrain with cultural fluency and measured judgment.',
                'Our International Relations & Diplomacy practice supports governments, institutions and corporations that need to engage across borders, protocols and political systems without losing time or credibility.',
            ],
            'services' => [
                'Bilateral engagement strategy' => 'Designing the sequence and substance of engagement between states, institutions and companies.',
                'Multilateral and institutional access' => 'Navigating regional blocs, development institutions and international organizations.',
                'Protocol and delegation support' => 'Managing visits, delegations and high-level meetings with the correct protocol and tone.',
                'Country entry and positioning' => 'Preparing leadership for new markets, including the political and cultural context that decides outcomes.',
                'Diplomatic risk assessment' => 'Evaluating how geopolitical developments can affect a project, partnership or investment.',
                'Cross-cultural negotiation support' => 'Preparing and supporting negotiations where language, custom and history shape the table.',
            ],
            'approach' => [
                'Understand' => 'We establish the interests of every party, including those not at the table.',
                'Prepare' => 'We shape the agenda, the messengers and the sequence of conversations.',
                'Convene' => 'We create the conditions for trust: neutral ground, credible introductions, patience.',
                'Follow through' => 'We carry commitments from handshake to implementation.',
            ],
            'outcomes' => [
                'Credible entry into new institutional environments',
                'Relationships built on respect rather than transaction',
                'Reduced diplomatic and reputational risk',
                'Clear, defensible positions in complex negotiations',
            ],
            'sectors' => ['government-public-sector', 'defense-security', 'finance-investments'],
            'engagement' => ['strategic-advisory-retainers', 'project-based-engagements'],
        ],

        'strategic-intelligence-analysis' => [
            'overview' => [
                'Good decisions need good information, delivered in time and in context. Our Strategic Intelligence & Analysis practice turns scattered signals into clear, decision-ready assessments of the political, economic and security environment.',
                'We work only from lawful, ethical sources: open-source research, expert networks, field relationships and rigorous analytic method. We do not trade in rumor, and we state plainly how confident we are in what we say.',
            ],
            'services' => [
                'Country and market risk assessments' => 'Structured evaluations of political, regulatory, economic and security conditions.',
                'Political and policy forecasting' => 'Scenario-based views of where events and decisions are heading, with explicit assumptions.',
                'Counterparty and stakeholder analysis' => 'Understanding the interests, networks and track record of the parties you are engaging.',
                'Rapid-response briefings' => 'Fast, concise analysis when an event changes the picture overnight.',
                'Horizon scanning' => 'Early identification of trends that will affect a sector or region over the next one to five years.',
                'Board and executive reporting' => 'Insight packaged for senior decision-makers: short, direct and actionable.',
            ],
            'approach' => [
                'Frame' => 'We agree the question, the decision it supports and the deadline.',
                'Gather' => 'We draw on open sources, expert networks and trusted field contacts.',
                'Assess' => 'We test competing explanations and state our confidence openly.',
                'Deliver' => 'We provide a clear recommendation, not a pile of material.',
            ],
            'outcomes' => [
                'Decisions made with a clearer view of risk',
                'Early warning of political and regulatory shifts',
                'Briefings leadership will actually read',
                'Greater confidence in counterparties and partners',
            ],
            'sectors' => ['defense-security', 'energy-natural-resources', 'finance-investments'],
            'engagement' => ['strategic-advisory-retainers', 'crisis-management-special-situations'],
        ],

        'investment-capital-strategy' => [
            'overview' => [
                'Capital follows confidence, and confidence depends on context. We help investors, sponsors and institutions structure investment strategy with a clear understanding of the political, regulatory and relationship landscape in which the capital will operate.',
                'Our practice works alongside, not in place of, a client’s legal, financial and technical advisors. We add the layer they cannot: access, context and judgment about how decisions are really made.',
            ],
            'services' => [
                'Investment thesis and market entry' => 'Testing an investment case against political and regulatory reality before capital is committed.',
                'Capital partner identification' => 'Introductions to aligned investors, development finance institutions and sovereign or strategic capital.',
                'Transaction and approval pathways' => 'Clarifying the approvals, counterparties and sequencing that determine a transaction’s timeline.',
                'Stakeholder alignment for large projects' => 'Bringing public, private and community stakeholders to a shared understanding.',
                'Portfolio political-risk review' => 'Assessing how developments in a market affect existing holdings.',
                'Investor and government dialogue' => 'Structuring constructive engagement between capital and the authorities that regulate it.',
            ],
            'approach' => [
                'Diagnose' => 'We assess the opportunity and the environment it sits in.',
                'Structure' => 'We help shape the strategy, partners and sequence of engagement.',
                'Introduce' => 'We open doors to the right capital and the right counterparties.',
                'Support' => 'We stay close through approvals, negotiations and execution.',
            ],
            'outcomes' => [
                'Investment cases grounded in local reality',
                'Better-aligned capital and partners',
                'Clearer, faster paths through approvals',
                'Reduced political and regulatory surprises',
            ],
            'sectors' => ['finance-investments', 'infrastructure-transportation', 'technology-innovation'],
            'engagement' => ['project-based-engagements', 'strategic-advisory-retainers'],
        ],

        'business-development-partnerships' => [
            'overview' => [
                'The best commercial opportunities are rarely advertised. They emerge through trusted introductions and well-prepared conversations. Our Business Development & Partnerships practice helps companies find, shape and close those opportunities across borders.',
                'We act as a trusted extension of a client’s own team: opening doors, preparing the ground and supporting the relationship until a partnership is productive.',
            ],
            'services' => [
                'Market and partner identification' => 'Finding the counterparties, distributors and joint-venture candidates worth pursuing.',
                'Introductions and relationship brokering' => 'Warm, credible introductions to decision-makers on both sides of a deal.',
                'Partnership structuring support' => 'Helping parties find a structure that works commercially and politically.',
                'Local presence and representation' => 'On-the-ground support in markets where a client has no footprint.',
                'Tender and procurement positioning' => 'Preparing for public and institutional procurement opportunities.',
                'Account and relationship stewardship' => 'Sustained attention that keeps partnerships healthy after signing.',
            ],
            'approach' => [
                'Qualify' => 'We confirm the opportunity is real and worth pursuing.',
                'Prepare' => 'We shape your story for the audience that matters.',
                'Connect' => 'We make the introductions and manage the conversation.',
                'Close and sustain' => 'We support the deal through signature and into delivery.',
            ],
            'outcomes' => [
                'Qualified opportunities in new markets',
                'Partnerships that are commercially and politically sound',
                'A trusted local presence without a permanent footprint',
                'Relationships that continue to produce value',
            ],
            'sectors' => ['technology-innovation', 'energy-natural-resources', 'infrastructure-transportation'],
            'engagement' => ['project-based-engagements', 'strategic-advisory-retainers'],
        ],

        'media-narrative-management' => [
            'overview' => [
                'In any serious engagement, perception matters as much as substance. Our Media & Narrative Management practice helps leaders shape how they, their institutions and their projects are understood by the audiences that matter.',
                'We favor accuracy, restraint and consistency. Durable reputation is built by saying the right thing clearly and repeatedly, and by being ready when it is tested.',
            ],
            'services' => [
                'Narrative development' => 'A clear, honest account of who a client is, what they stand for and why it matters.',
                'Executive communications' => 'Speeches, op-eds, talking points and statements for senior leaders.',
                'Media relations' => 'Credible relationships with editors and journalists, grounded in respect for their work.',
                'Reputation and issues management' => 'Preparing for, and responding to, challenges to a client’s standing.',
                'Stakeholder communications' => 'Messaging tailored to governments, investors, communities and partners.',
                'Media training and preparation' => 'Equipping principals to speak with confidence under pressure.',
            ],
            'approach' => [
                'Audit' => 'We assess how you are currently perceived, and by whom.',
                'Define' => 'We agree the story, the audiences and the tone.',
                'Deliver' => 'We equip you with the messages, messengers and channels.',
                'Protect' => 'We monitor, adjust and stand ready for the unexpected.',
            ],
            'outcomes' => [
                'A consistent narrative across every audience',
                'Stronger relationships with media and opinion leaders',
                'Leaders who are prepared and composed in public',
                'Faster, calmer response when reputation is tested',
            ],
            'sectors' => ['government-public-sector', 'technology-innovation', 'finance-investments'],
            'engagement' => ['strategic-advisory-retainers', 'crisis-management-special-situations'],
        ],

        'ppp-infrastructure-advisory' => [
            'overview' => [
                'Major infrastructure succeeds when public purpose, private capital and community acceptance line up. That alignment is difficult, slow and political. Our PPP & Infrastructure Advisory practice helps all parties reach it.',
                'We support governments preparing to partner with the private sector, and sponsors, operators and financiers seeking to participate in ports, corridors, energy systems and urban networks.',
            ],
            'services' => [
                'PPP strategy and structuring support' => 'Framing the partnership model, risk allocation and stakeholder roles.',
                'Project positioning' => 'Presenting a project to authorities, financiers and communities in terms each can support.',
                'Government and agency engagement' => 'Navigating the ministries, regulators and sub-national bodies a project depends on.',
                'Financier and sponsor introductions' => 'Connecting projects with development finance, institutional capital and experienced operators.',
                'Community and stakeholder engagement' => 'Building social license through early, respectful engagement.',
                'Delivery-phase support' => 'Supporting relationships through procurement, financial close and construction.',
            ],
            'approach' => [
                'Assess' => 'We test the project’s political, social and financial foundations.',
                'Align' => 'We bring public, private and community interests toward one position.',
                'Mobilize' => 'We connect the project with capital and capable partners.',
                'Deliver' => 'We keep stakeholders aligned through the long road to completion.',
            ],
            'outcomes' => [
                'Projects that reach financial close',
                'Aligned public and private stakeholders',
                'Stronger community acceptance',
                'Clearer risk allocation between partners',
            ],
            'sectors' => ['infrastructure-transportation', 'energy-natural-resources', 'finance-investments'],
            'engagement' => ['project-based-engagements', 'government-affairs-lobbying'],
        ],

        'think-tank-strategic-advisory' => [
            'overview' => [
                'Underground’s think-tank practice is where independent research meets practical advice. We convene experts, publish original analysis and advise leaders on long-horizon questions of policy, markets and security.',
                'The work serves two purposes: it sharpens the counsel we give clients, and it contributes to a wider, better-informed public conversation.',
            ],
            'services' => [
                'Policy research and white papers' => 'Original, rigorous analysis on questions of public policy and international affairs.',
                'Expert convenings' => 'Closed-door roundtables and forums that bring the right voices together.',
                'Long-horizon strategy' => 'Scenario work and strategic planning for institutions with multi-year views.',
                'Leadership dialogues' => 'Structured conversations between senior leaders across government, business and academia.',
                'Capacity building' => 'Workshops and advisory support to strengthen institutional analytic capability.',
                'Published insights' => 'Our own analysis, shared through the Insights section of this site.',
            ],
            'approach' => [
                'Question' => 'We start with a question that matters, not a conclusion we want.',
                'Research' => 'We assemble evidence and expert perspective.',
                'Test' => 'We challenge findings with practitioners who have to live with them.',
                'Share' => 'We publish or brief, in the form that best serves the audience.',
            ],
            'outcomes' => [
                'Clearer thinking on complex, long-term questions',
                'Informed leaders and stronger institutions',
                'Original analysis that moves a conversation forward',
                'A trusted network of experts and practitioners',
            ],
            'sectors' => ['government-public-sector', 'defense-security', 'technology-innovation'],
            'engagement' => ['strategic-advisory-retainers', 'project-based-engagements'],
        ],

    ],

    'sectors' => [

        'government-public-sector' => [
            'overview' => [
                'Governments and public institutions face a unique pressure: decisions are scrutinized, consequences are public and trust is the currency of everything. We support heads of state, ministries and public bodies with counsel that respects both the weight of the office and the need for discretion.',
                'Our work helps public leaders position their agenda internationally, attract and structure investment, and manage the relationships that determine whether a policy succeeds.',
            ],
            'focus' => [
                'Policy positioning and international engagement',
                'Investment attraction and public-private partnership',
                'Institutional communications and public narrative',
                'Delegation, protocol and high-level meeting support',
                'Strategic planning for national and regional priorities',
            ],
            'clients' => ['Heads of state and cabinets', 'Ministries and public agencies', 'Regional and multilateral bodies', 'State-owned enterprises'],
            'capabilities' => ['government-political-affairs', 'international-relations-diplomacy', 'think-tank-strategic-advisory'],
        ],

        'energy-natural-resources' => [
            'overview' => [
                'Energy and resources sit at the heart of national strategy and global competition. The energy transition is reshaping who holds leverage, and where capital, technology and policy will converge.',
                'We help producers, investors and governments navigate licensing, partnership, public acceptance and geopolitics: the issues that decide whether a resource project creates lasting value.',
            ],
            'focus' => [
                'Licensing, concessions and regulatory engagement',
                'Partner and investor identification for energy projects',
                'Energy-transition strategy and positioning',
                'Community and stakeholder engagement',
                'Geopolitical and supply-chain risk analysis',
            ],
            'clients' => ['National and international energy companies', 'Mining and resource operators', 'Resource-owning governments', 'Energy investors and funds'],
            'capabilities' => ['government-political-affairs', 'investment-capital-strategy', 'strategic-intelligence-analysis'],
        ],

        'infrastructure-transportation' => [
            'overview' => [
                'Ports, rail, roads, airports and digital networks are where capital becomes strategic outcome. They are also long-horizon, politically sensitive and dependent on many parties acting together.',
                'We advise the institutions that plan, finance, build and operate these assets, helping them align stakeholders and keep projects moving through the long arc from concept to completion.',
            ],
            'focus' => [
                'PPP strategy and project positioning',
                'Government and regulator engagement',
                'Financier and operator introductions',
                'Community acceptance and stakeholder alignment',
                'Corridor and connectivity strategy',
            ],
            'clients' => ['Infrastructure developers and sponsors', 'Transport and logistics operators', 'Public authorities and agencies', 'Development finance institutions'],
            'capabilities' => ['ppp-infrastructure-advisory', 'investment-capital-strategy', 'government-political-affairs'],
        ],

        'defense-security' => [
            'overview' => [
                'Defense and security relationships are built on trust that takes years to earn and a moment to lose. The stakes are high, the circles are small and the margin for error is thin.',
                'We provide calibrated, lawful counsel to institutions and companies working in and around the security domain: political context, relationship strategy and risk analysis, handled with the discretion the field demands.',
            ],
            'focus' => [
                'Strategic and geopolitical risk analysis',
                'Government and institutional relationship strategy',
                'Partnership and market-entry advisory within applicable law',
                'Security-sector policy research',
                'Crisis and special-situations support',
            ],
            'clients' => ['Defense and security companies', 'Government and institutional buyers', 'Allied and partner institutions', 'Investors in the security domain'],
            'capabilities' => ['strategic-intelligence-analysis', 'international-relations-diplomacy', 'think-tank-strategic-advisory'],
        ],

        'technology-innovation' => [
            'overview' => [
                'Technology moves faster than the institutions that govern it, and sovereigns increasingly depend on technology they do not build. Innovators and governments need each other, and rarely know how to begin the conversation.',
                'We position technology ventures with the public institutions, partners and capital that can scale them, and we help governments engage technology with clarity about both opportunity and risk.',
            ],
            'focus' => [
                'Government and public-sector market entry',
                'Regulatory and policy engagement',
                'Strategic partner and investor introductions',
                'Digital-infrastructure and data-policy advisory',
                'Narrative and positioning for emerging technologies',
            ],
            'clients' => ['Technology companies and scale-ups', 'Public institutions adopting technology', 'Venture and growth investors', 'Research and innovation bodies'],
            'capabilities' => ['business-development-partnerships', 'media-narrative-management', 'investment-capital-strategy'],
        ],

        'finance-investments' => [
            'overview' => [
                'Capital allocators rarely see the political and strategic context their investments depend on until it moves against them. We give that context a seat at the table from the start.',
                'We work with funds, institutions and sponsors to understand the environments they invest in, identify aligned partners and structure engagement with the authorities that shape returns.',
            ],
            'focus' => [
                'Investment thesis testing against political reality',
                'Partner, co-investor and counterparty introductions',
                'Regulatory and approvals pathway planning',
                'Portfolio political-risk monitoring',
                'Investor communications and positioning',
            ],
            'clients' => ['Funds and asset managers', 'Development finance institutions', 'Corporate and strategic investors', 'Family offices and sovereign capital'],
            'capabilities' => ['investment-capital-strategy', 'strategic-intelligence-analysis', 'business-development-partnerships'],
        ],

    ],

    'engagement_models' => [

        'strategic-advisory-retainers' => [
            'overview' => [
                'A retainer gives a client continuous access to the practice: a trusted counselor who knows the context, the people and the stakes, and who is available when it matters.',
                'It suits principals and institutions that face a steady flow of strategic decisions and want a long-term partner rather than a series of one-off projects.',
            ],
            'suited_for' => [
                'Leaders facing ongoing, high-stakes decisions',
                'Institutions building a long-term international position',
                'Companies operating across several politically complex markets',
            ],
            'includes' => [
                'A dedicated senior partner and working team',
                'Regular strategic reviews and forward-looking briefings',
                'On-call access for time-sensitive questions',
                'Introductions and relationship support as needed',
                'Confidential reporting tailored to the client',
            ],
            'process' => [
                'Scoping' => 'We agree priorities, cadence and who sits on the client team.',
                'Onboarding' => 'We learn the organization, its history and its sensitivities.',
                'Rhythm' => 'We establish regular reviews alongside rapid-response access.',
                'Review' => 'Scope and value are reassessed together, at least annually.',
            ],
            'commitment' => 'Typically twelve months, renewable. Fees are fixed and agreed in advance.',
        ],

        'project-based-engagements' => [
            'overview' => [
                'Some objectives have a clear beginning and end: a market entry, a transaction, a negotiation, a project to reach financial close. For these we scope a defined engagement with clear deliverables and a clear horizon.',
                'Project engagements concentrate the right people on a single outcome and end when it is achieved.',
            ],
            'suited_for' => [
                'Market entry or expansion into a new country',
                'Transactions and partnerships with a defined objective',
                'Infrastructure and large-project stakeholder alignment',
            ],
            'includes' => [
                'A written scope, timeline and success criteria',
                'A named lead and a team matched to the objective',
                'Milestone reviews with the client',
                'Relevant introductions, analysis and preparation',
                'A closing review and handover of relationships and materials',
            ],
            'process' => [
                'Define' => 'We agree the objective, deliverables and measures of success.',
                'Plan' => 'We design the approach and assign the team.',
                'Execute' => 'We deliver against milestones with regular client reviews.',
                'Close' => 'We conclude with a review, handover and recommendations for next steps.',
            ],
            'commitment' => 'Scoped to the objective, usually three to nine months. Fees are agreed per project.',
        ],

        'government-affairs-lobbying' => [
            'overview' => [
                'When a client’s interests depend on public decisions, structured government-affairs support gives them a lawful, professional voice. We design and run engagement programs aimed at the institutions and officials who shape policy.',
                'All activity is conducted in line with applicable lobbying, disclosure and ethics requirements in each jurisdiction.',
            ],
            'suited_for' => [
                'Companies affected by pending legislation or regulation',
                'Industry groups seeking a coordinated position',
                'Organizations seeking public funding, licenses or approvals',
            ],
            'includes' => [
                'Stakeholder mapping and an engagement plan',
                'Policy analysis and position papers',
                'Meetings and introductions with relevant officials',
                'Coalition building with aligned groups',
                'Compliance with local registration and disclosure rules',
            ],
            'process' => [
                'Assess' => 'We analyze the issue, the decision-makers and the timeline.',
                'Plan' => 'We agree objectives, messages and a compliant engagement plan.',
                'Engage' => 'We carry out meetings, submissions and coalition work.',
                'Report' => 'We report progress and adjust to political developments.',
            ],
            'commitment' => 'Retainer or project basis depending on the issue; always documented and compliant with local law.',
        ],

        'crisis-management-special-situations' => [
            'overview' => [
                'Crises do not wait for a convenient moment. When a political shock, reputational threat or sudden dispute puts an organization at risk, speed, composure and discretion decide the outcome.',
                'We assemble a small, senior team quickly to assess the situation, advise leadership and manage the critical relationships and communications.',
            ],
            'suited_for' => [
                'Organizations facing a sudden political or reputational threat',
                'Leaders in disputes with governments or major counterparties',
                'Situations requiring immediate, confidential senior counsel',
            ],
            'includes' => [
                'Rapid situation assessment and options for leadership',
                'Senior-led crisis communications and stakeholder outreach',
                'Government and counterparty engagement',
                'Coordination with the client’s legal and security advisors',
                'Post-crisis review and resilience recommendations',
            ],
            'process' => [
                'Respond' => 'We engage within hours and stabilize the situation.',
                'Assess' => 'We establish the facts and the options.',
                'Act' => 'We manage communications and key relationships.',
                'Recover' => 'We review, rebuild and strengthen the organization against recurrence.',
            ],
            'commitment' => 'Mobilized on short notice for as long as the situation requires. Terms agreed at the outset.',
        ],

    ],

];
