<?php
/**
 * WP-CLI seed command for the Northium content backbone.
 *
 * Run via:
 *   wp northium seed                  insert if missing (idempotent)
 *   wp northium seed --reset          delete all seed posts first
 *
 * Seeds 5 divisions, 10 services, 5 advisors, 5 articles, 1 campaign.
 * Content positions Northium as fee-only, plain-language, modern — built
 * around 5 distinct divisions (Wealth, Tax & Estate, Family CFO, Founder &
 * Equity, Studio) rather than the generic financial-services lineup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

class Northium_CMS_Seed_Command {

	public function seed( $args, $assoc_args ): void {
		$reset = isset( $assoc_args['reset'] );

		if ( $reset ) {
			$this->wipe();
			WP_CLI::log( 'Wiped existing content.' );
		}

		$divisions = $this->seed_divisions();
		WP_CLI::log( sprintf( 'Divisions: %d', count( $divisions ) ) );

		$services = $this->seed_services( $divisions );
		WP_CLI::log( sprintf( 'Services:  %d', count( $services ) ) );

		$advisors = $this->seed_advisors( $divisions );
		WP_CLI::log( sprintf( 'Advisors:  %d', count( $advisors ) ) );

		$articles = $this->seed_articles( $divisions, $advisors );
		WP_CLI::log( sprintf( 'Articles:  %d', count( $articles ) ) );

		$campaigns = $this->seed_campaigns( $divisions );
		WP_CLI::log( sprintf( 'Campaigns: %d', count( $campaigns ) ) );

		$this->backfill_division_features( $divisions, $services, $advisors );

		WP_CLI::success( 'Seed complete.' );
	}

	private function wipe(): void {
		foreach ( array( 'campaign', 'article', 'advisor', 'service', 'division' ) as $cpt ) {
			$ids = get_posts(
				array(
					'post_type'        => $cpt,
					'post_status'      => 'any',
					'numberposts'      => -1,
					'fields'           => 'ids',
					'no_found_rows'    => true,
					'suppress_filters' => true,
				)
			);
			foreach ( $ids as $id ) {
				wp_delete_post( $id, true );
			}
		}
	}

	private function seed_divisions(): array {
		// Accent colors are archetype-driven:
		//   Sage      -> deep navy        (Wealth)
		//   Sage/Magician -> forest green (Tax & Estate)
		//   Caregiver/Ruler -> warm slate (Family CFO)
		//   Magician  -> deep indigo      (Founder & Equity)
		//   Creator   -> terracotta       (Studio)
		$data = array(
			'wealth-planning' => array(
				'title'        => 'Wealth Planning',
				'short'        => 'Fee-only investment and retirement planning. No kickbacks, no in-house funds.',
				'full'         => 'Wealth Planning at Northium is built around one rule: we do not sell products. Our portfolios use low-cost ETFs from third-party providers, our advice is fee-only, and our recommendations do not change based on what pays us better. The result is a financial picture that adds up over decades, not one optimized for someone else\'s commission.',
				'accent_color' => '#1a365d',
				'order'        => 10,
			),
			'tax-and-estate' => array(
				'title'        => 'Tax & Estate',
				'short'        => 'Year-round tax work and estate planning, not just April fire drills.',
				'full'         => 'Most tax work happens in March. Most of the value happens earlier. Our tax and estate team works through the year — capital gains harvesting, RRSP and TFSA optimization, registered plan strategy, and estate documents reviewed before life changes, not after. Coordinated with your wealth plan, not bolted on.',
				'accent_color' => '#2f5d4a',
				'order'        => 20,
			),
			'family-cfo' => array(
				'title'        => 'Family CFO',
				'short'        => 'Bill management, cash-flow tracking, and household finance for complex households.',
				'full'         => 'Once a household passes a certain complexity, day-to-day finance becomes its own job: rental properties, multiple accounts, household payroll, charitable giving, kids\' education accounts. Family CFO handles the operational layer so your financial plan actually gets executed instead of just sitting in a binder.',
				'accent_color' => '#6a7d8a',
				'order'        => 30,
			),
			'founder-and-equity' => array(
				'title'        => 'Founder & Equity',
				'short'        => 'Stock comp, RSUs, options, secondary sales, and exit planning for founders and executives.',
				'full'         => 'Equity is most of the upside in a tech career, and almost none of the standard advice fits it. We work with founders and senior operators on RSU vesting math, 83(b) elections, ISO and NSO planning, secondary sales, qualified small business stock, and what to do the week of an acquisition. Specific to equity, not retrofitted from standard wealth advice.',
				'accent_color' => '#4a3d7e',
				'order'        => 40,
			),
			'studio' => array(
				'title'        => 'Studio',
				'short'        => 'Northium\'s research desk. We publish what we have learned so clients are not the only ones who benefit.',
				'full'         => 'Studio is our internal research and editorial team. We write the guides, model the scenarios, and publish what we have worked out for clients — anonymized — so the next person facing the same question gets a head start. Not marketing, not thought leadership; the actual work.',
				'accent_color' => '#b3614c',
				'order'        => 50,
			),
		);

		$ids = array();
		foreach ( $data as $slug => $row ) {
			$ids[ $slug ] = $this->upsert_post(
				'division',
				$slug,
				array(
					'post_title'   => $row['title'],
					'post_content' => $row['full'],
					'post_excerpt' => $row['short'],
					'menu_order'   => $row['order'],
				),
				array(
					'accent_color' => $row['accent_color'],
				)
			);
		}
		return $ids;
	}

	private function seed_services( array $divisions ): array {
		$data = array(
			'the-annual-plan'         => array( 'div' => 'wealth-planning',     'title' => 'The Annual Plan',          'summary' => 'A written plan you actually read, refreshed every year.', 'cta' => 'Start with a plan' ),
			'portfolio-build'         => array( 'div' => 'wealth-planning',     'title' => 'Portfolio Build',          'summary' => 'Fee-only portfolios. Third-party ETFs. No kickbacks, no in-house funds.', 'cta' => 'Build a portfolio' ),
			'year-round-tax-review'   => array( 'div' => 'tax-and-estate',      'title' => 'Year-Round Tax Review',    'summary' => 'Quarterly check-ins so March stops being a fire drill.', 'cta' => 'Start the review' ),
			'estate-documents-audit'  => array( 'div' => 'tax-and-estate',      'title' => 'Estate Documents Audit',   'summary' => 'Will, POA, and beneficiary designations reviewed before life changes them for you.', 'cta' => 'Audit your docs' ),
			'bill-pay-bookkeeping'    => array( 'div' => 'family-cfo',          'title' => 'Bill Pay & Bookkeeping',   'summary' => 'Household bills, statements, and reconciliation, professionally managed.', 'cta' => 'Hand it off' ),
			'household-cash-flow'     => array( 'div' => 'family-cfo',          'title' => 'Household Cash-Flow',      'summary' => 'Multi-account, multi-property cash-flow visibility in one monthly report.', 'cta' => 'See an example' ),
			'rsu-options-planning'    => array( 'div' => 'founder-and-equity',  'title' => 'RSU & Options Planning',   'summary' => 'Vesting calendars, 10b5-1 plans, exercise strategy, AMT modelling.', 'cta' => 'Plan your equity' ),
			'liquidity-event-prep'    => array( 'div' => 'founder-and-equity',  'title' => 'Liquidity Event Prep',     'summary' => 'What to do in the 90 days before a tender, secondary, or acquisition closes.', 'cta' => 'Get the playbook' ),
			'research-briefs'         => array( 'div' => 'studio',              'title' => 'Research Briefs',          'summary' => 'Plain-language explanations of the questions clients ask us most.', 'cta' => 'Read the briefs' ),
			'scenario-modeling'       => array( 'div' => 'studio',              'title' => 'Scenario Modeling',        'summary' => 'Custom financial models for one-time decisions worth getting right.', 'cta' => 'Request a model' ),
		);

		$ids = array();
		foreach ( $data as $slug => $row ) {
			$ids[ $slug ] = $this->upsert_post(
				'service',
				$slug,
				array(
					'post_title'   => $row['title'],
					'post_excerpt' => $row['summary'],
					'post_content' => $row['summary'],
				),
				array(
					'division_id' => $divisions[ $row['div'] ] ?? 0,
					'cta_label'   => $row['cta'],
					'cta_url'     => '/contact?service=' . $slug,
				)
			);
		}
		return $ids;
	}

	private function seed_advisors( array $divisions ): array {
		$data = array(
			'maya-lindqvist' => array( 'name' => 'Maya Lindqvist', 'role' => 'Lead Planner, Wealth',     'div' => 'wealth-planning',    'email' => 'maya@northium.example' ),
			'daniel-osei'    => array( 'name' => 'Daniel Osei',    'role' => 'Tax & Estate Director',    'div' => 'tax-and-estate',     'email' => 'daniel@northium.example' ),
			'hana-yamada'    => array( 'name' => 'Hana Yamada',    'role' => 'Family CFO Lead',          'div' => 'family-cfo',         'email' => 'hana@northium.example' ),
			'marcus-reyes'   => array( 'name' => 'Marcus Reyes',   'role' => 'Founder Services Lead',    'div' => 'founder-and-equity', 'email' => 'marcus@northium.example' ),
			'iris-bergstrom' => array( 'name' => 'Iris Bergstrom', 'role' => 'Studio / Research Editor', 'div' => 'studio',             'email' => 'iris@northium.example' ),
		);

		$bios = array(
			'maya-lindqvist' => 'Maya leads our wealth practice. Twelve years working with mid-career professionals and dual-income households. She built our model portfolios and runs the quarterly review process. Outside Northium she teaches a personal-finance course at the local university.',
			'daniel-osei'    => 'Daniel runs the tax and estate practice. Former Big Four tax practitioner, now happily working with fewer clients on the work that actually moves the needle. He is the person to talk to about capital gains, donor-advised funds, and what your will should actually say.',
			'hana-yamada'    => 'Hana built the Family CFO service from scratch. Her background is in operations, not investing — which is exactly the point. Family CFO is about the work that makes a financial plan actually executable: bills, books, reporting, and the household team that supports it.',
			'marcus-reyes'   => 'Marcus runs Founder & Equity. Earlier career on the equity desk at a tier-one bank, then in-house finance at two venture-backed companies. He has been through three liquidity events as an employee and four as an advisor — which is the experience set you actually want here.',
			'iris-bergstrom' => 'Iris edits Studio. She picks the questions worth answering, runs the models that back the answers, and writes the briefs that go to clients and to the public. The internal joke is that the briefs are the most-clicked thing we ship, which says something about what people actually want from advisors.',
		);

		$ids = array();
		foreach ( $data as $slug => $row ) {
			$ids[ $slug ] = $this->upsert_post(
				'advisor',
				$slug,
				array(
					'post_title'   => $row['name'],
					'post_content' => $bios[ $slug ],
				),
				array(
					'role'         => $row['role'],
					'division_ids' => array( $divisions[ $row['div'] ] ?? 0 ),
					'email'        => $row['email'],
					'linkedin_url' => 'https://www.linkedin.com/in/' . $slug . '/',
				)
			);
		}
		return $ids;
	}

	private function seed_articles( array $divisions, array $advisors ): array {
		$data = array(
			'liquidity-event-week-of' => array(
				'title' => 'What to do the week your company gets acquired',
				'div'   => 'founder-and-equity',
				'author'=> 'marcus-reyes',
				'body'  => '<p>The week of an acquisition close is the highest-stakes financial week most employees will ever have. Here is the short list of decisions that actually matter, and the ones that do not.</p><p>First: do not pre-spend the proceeds. The deal is not closed until the deal is closed, and even then the wire takes its own time. The cash you imagined is not yours yet.</p><p>Second: lock in your exercise window math. ISOs have an AMT trap that tightens the moment your shares become liquid. Coordinate with your tax team before, not after.</p>',
			),
			'500k-at-40-honest-answer' => array(
				'title' => 'I have $500k saved at 40 — am I behind?',
				'div'   => 'wealth-planning',
				'author'=> 'maya-lindqvist',
				'body'  => '<p>This is the question we get most often, and the honest answer is: it depends on three things, none of which is the $500k number itself.</p><p>The three are your savings rate going forward, your spending in retirement, and how many years you plan to keep working. Two of those are choices, not fates. The math gets a lot kinder once you stop treating retirement as a single date and start treating it as a ramp.</p>',
			),
			'three-rsu-mistakes' => array(
				'title' => 'The three RSU mistakes we see most often',
				'div'   => 'founder-and-equity',
				'author'=> 'marcus-reyes',
				'body'  => '<p>RSU comp creates a specific kind of tax pain that most general advice misses. Three patterns we see repeatedly:</p><p>First, treating vested RSUs as a windfall instead of regular income. They are taxed as income at vest. Spending them as bonus cash without setting aside the tax owed is the single most common mistake we fix.</p><p>Second, holding all vested shares because the stock is going up. Concentration risk is real even when the trend is your friend. The right move is usually a written sell-on-vest discipline.</p><p>Third, missing the AMT trap on ISO exercises. This one needs a real conversation, not a blog post.</p>',
			),
			'tax-work-happens-in-november' => array(
				'title' => 'Why your tax bill arrives in March but the work happens in November',
				'div'   => 'tax-and-estate',
				'author'=> 'daniel-osei',
				'body'  => '<p>Filing season is the wrong time to do tax work. By April, the year is closed and most of the levers have stopped working. The year-end window — roughly the last 8 weeks of the year — is when actual tax savings get captured.</p><p>Capital gains and losses can still be matched. Charitable giving can still be timed across cash, securities, and donor-advised funds. Registered plan contributions can still be sized correctly. RSU exercise windows are still open. None of those moves work in March.</p>',
			),
			'when-your-household-needs-a-cfo' => array(
				'title' => 'When your household needs a CFO (and when it does not)',
				'div'   => 'family-cfo',
				'author'=> 'hana-yamada',
				'body'  => '<p>Most households do not need a CFO. The work is straightforward: a few accounts, a couple of credit cards, employer-deducted savings. A spreadsheet handles it.</p><p>The threshold where it stops being straightforward is not a dollar figure — it is a complexity figure. Multiple properties. Several investment accounts. Household employees. Complex charitable giving. International family. Once two or three of those stack, the operational layer becomes its own job, and the financial plan stops getting executed because no one is doing the operational work.</p>',
			),
		);

		$ids = array();
		foreach ( $data as $slug => $row ) {
			$ids[ $slug ] = $this->upsert_post(
				'article',
				$slug,
				array(
					'post_title'   => $row['title'],
					'post_content' => $row['body'],
					'post_excerpt' => wp_trim_words( wp_strip_all_tags( $row['body'] ), 28 ),
				),
				array(
					'division_id'       => $divisions[ $row['div'] ] ?? 0,
					'author_advisor_id' => $advisors[ $row['author'] ] ?? 0,
				)
			);
		}
		return $ids;
	}

	private function seed_campaigns( array $divisions ): array {
		$ids  = array();
		$slug = 'year-end-tax-review-2026';
		$ids[ $slug ] = $this->upsert_post(
			'campaign',
			$slug,
			array(
				'post_title'   => 'Year-End Tax Review 2026',
				'post_excerpt' => 'A 4-week sprint to capture the moves that only work before December 31.',
				'post_content' => '',
			),
			array(
				'target_division_id' => $divisions['tax-and-estate'] ?? 0,
				'hero_headline'      => 'Don\'t lose Q4 to your tax bill.',
				'hero_subtext'       => 'A 4-week sprint to capture the moves that only work before December 31 — capital gains harvesting, charitable timing, registered plan top-ups.',
				'cta_text'           => 'Book your year-end review',
				'cta_url'            => '/contact?campaign=year-end-tax-2026',
				'sections'           => array(
					array(
						'heading' => 'Why a sprint, not a deadline scramble',
						'body'    => 'Most tax-saving moves require positioning before the year ends — but they only work if you have reviewed your full picture, not just one account. The sprint format forces a complete review with time to actually act.',
					),
					array(
						'heading' => 'What we cover',
						'body'    => 'Capital gains harvesting and tax-loss matching. Charitable giving timing across cash, securities, and donor-advised funds. RRSP and FHSA contribution math. Estate document refresh. RSU and option exercise windows. Owner compensation if you control a corporation.',
					),
					array(
						'heading' => 'Who this is for',
						'body'    => 'Households with at least one of: capital gains over $50k for the year, equity comp vesting in Q4, complex charitable giving, or a corporation in the family. If you do not see yourself in that list, the standard year-end checklist is probably enough.',
					),
				),
				'testimonials' => array(
					array(
						'quote'       => 'We rescued $34,000 in deductions in three weeks of focused work. None of it was complicated — it just needed someone paying attention before December 31.',
						'attribution' => 'Tech executive',
						'role'        => 'Toronto, year-end 2025 sprint',
					),
				),
			)
		);
		return $ids;
	}

	private function backfill_division_features( array $divisions, array $services, array $advisors ): void {
		$features = array(
			'wealth-planning'    => array( 'services' => array( 'the-annual-plan', 'portfolio-build' ),                'advisors' => array( 'maya-lindqvist' ) ),
			'tax-and-estate'     => array( 'services' => array( 'year-round-tax-review', 'estate-documents-audit' ),  'advisors' => array( 'daniel-osei' ) ),
			'family-cfo'         => array( 'services' => array( 'bill-pay-bookkeeping', 'household-cash-flow' ),       'advisors' => array( 'hana-yamada' ) ),
			'founder-and-equity' => array( 'services' => array( 'rsu-options-planning', 'liquidity-event-prep' ),      'advisors' => array( 'marcus-reyes' ) ),
			'studio'             => array( 'services' => array( 'research-briefs', 'scenario-modeling' ),              'advisors' => array( 'iris-bergstrom' ) ),
		);

		foreach ( $features as $div_slug => $picks ) {
			$div_id = $divisions[ $div_slug ] ?? 0;
			if ( ! $div_id ) {
				continue;
			}
			$svc_ids = array_values( array_filter( array_map( fn( $s ) => $services[ $s ] ?? 0, $picks['services'] ) ) );
			$adv_ids = array_values( array_filter( array_map( fn( $a ) => $advisors[ $a ] ?? 0, $picks['advisors'] ) ) );
			update_post_meta( $div_id, 'featured_services', $svc_ids );
			update_post_meta( $div_id, 'featured_advisors', $adv_ids );
		}
	}

	private function upsert_post( string $cpt, string $slug, array $post_args, array $meta = array() ): int {
		$existing = get_posts(
			array(
				'name'          => $slug,
				'post_type'     => $cpt,
				'post_status'   => 'any',
				'numberposts'   => 1,
				'fields'        => 'ids',
				'no_found_rows' => true,
			)
		);

		$base = array_merge(
			array(
				'post_type'   => $cpt,
				'post_status' => 'publish',
				'post_name'   => $slug,
			),
			$post_args
		);

		if ( ! empty( $existing ) ) {
			$id         = (int) $existing[0];
			$base['ID'] = $id;
			wp_update_post( $base );
		} else {
			$id = (int) wp_insert_post( $base );
		}

		foreach ( $meta as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}

		return $id;
	}
}

WP_CLI::add_command( 'northium', 'Northium_CMS_Seed_Command' );
