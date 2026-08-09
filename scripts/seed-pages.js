/**
 * TLTH Page Seeder
 * Creates/updates WordPress pages via the WP REST API.
 *
 * Usage:
 *   node -r dotenv/config scripts/seed-pages.js
 *
 * Requires dotenv: npm install dotenv --save-dev
 */

const WP_URL           = process.env.WP_URL;
const WP_USER          = process.env.WP_USER;
const WP_APP_PASSWORD  = process.env.WP_APP_PASSWORD;

if (!WP_URL || !WP_USER || !WP_APP_PASSWORD) {
    console.error('Missing .env variables: WP_URL, WP_USER, WP_APP_PASSWORD');
    process.exit(1);
}

const auth    = Buffer.from(`${WP_USER}:${WP_APP_PASSWORD}`).toString('base64');
const headers = {
    'Authorization': `Basic ${auth}`,
    'Content-Type': 'application/json',
};

// ─── Helpers ──────────────────────────────────────────────────────────────────

async function wpGet(path) {
    const res = await fetch(`${WP_URL}/wp-json${path}`, { headers });
    if (!res.ok) throw new Error(`GET ${path} → ${res.status} ${res.statusText}`);
    return res.json();
}

async function wpPost(path, body) {
    const res = await fetch(`${WP_URL}/wp-json${path}`, {
        method: 'POST',
        headers,
        body: JSON.stringify(body),
    });
    if (!res.ok) {
        const text = await res.text();
        throw new Error(`POST ${path} → ${res.status}: ${text}`);
    }
    return res.json();
}

async function setAcfFields(pageId, fields) {
    // ACF REST API v3
    return wpPost(`/acf/v3/pages/${pageId}`, { fields });
}

// ─── Page definitions ─────────────────────────────────────────────────────────

const pages = [
    {
        title:    'À propos',
        slug:     'a-propos',
        template: 'templates/a-propos.php',
        acf: {
            title:               'Notre /*histoire*/',
            subtitle:            '',
            hero_image:          null,
            intro_title:         'Ton Livre Ton Histoire: bien /*plus que des livres!*/',
            intro_description:   '<p>Chez Ton Livre Ton Histoire, nous croyons que chaque enfant mérite de se voir comme le héros de sa propre aventure. Nos livres personnalisés sont conçus pour éveiller l\'imagination, nourrir la curiosité et renforcer la confiance en soi.</p>',
            intro_image:         null,
            intro_cta:           null,
            confiance_title:     'De la curiosité à la /*confiance en soi*/',
            confiance_description: '<p>Lorsqu\'un enfant tient entre ses mains un livre où il est le personnage principal, quelque chose de magique se produit. Il se voit capable de vivre de grandes aventures, d\'explorer des mondes imaginaires et de surmonter les défis qui se présentent à lui.</p>',
            confiance_image:     null,
            confiance_cta:       null,
            // unique-statement
            main_text:           'notre mission: /*donner la piqûre de la lecture*/',
            // features-grid (populated manually in WP admin)
            // team-carousel (populated manually in WP admin)
        },
    },
    {
        title:    'École / Garderie',
        slug:     'ecole-garderie',
        template: 'templates/ecole.php',
        acf: {
            title:                   '/*Éducation*/',
            subtitle:                'Enseigner, c\'est tout donner. Nous sommes là pour vous aider.',
            hero_image:              null,
            // unique-statement
            main_text:               'nous croyons en votre /*travail*/, et nous souhaitons vous donner les moyens de multiplier les moments de /*magic*/',
            // promo code split
            promo_code_title:        'Demandez votre /*code promotionnel*/',
            promo_code_description:  '<p>Vous pouvez faire une commande de groupe pour vos élèves ou accidentés. Profitez d\'un code spécial qui vous permet d\'économiser plus de 10 exemplaires.</p>',
            promo_code_image:        null,
            promo_code_cta:          { title: 'Je veux mon code', url: '#' },
            // discount-section (populated manually in WP admin)
            // campaign-steps (populated manually in WP admin)
            // cta-banner
            headline:                'Recevez 7$ par livre vendu!',
            background_color:        'teal',
            cta_link:                { title: 'Rejoignez la campagne', url: '#' },
        },
    },
];

// ─── Runner ───────────────────────────────────────────────────────────────────

async function upsertPage(def) {
    // Check if page already exists by slug
    let existingPages;
    try {
        existingPages = await wpGet(`/wp/v2/pages?slug=${def.slug}&per_page=1`);
    } catch (err) {
        existingPages = [];
    }

    let page;
    if (existingPages.length > 0) {
        page = existingPages[0];
        console.log(`  ↩  "${def.title}" already exists (ID: ${page.id})`);
    } else {
        console.log(`  +  Creating "${def.title}"…`);
        page = await wpPost('/wp/v2/pages', {
            title:  def.title,
            slug:   def.slug,
            status: 'publish',
        });
        console.log(`  ✓  Created (ID: ${page.id})`);
    }

    // Try to assign the template (only works after the template file is deployed to the server)
    try {
        await wpPost(`/wp/v2/pages/${page.id}`, { template: def.template });
        console.log(`  ✓  Template set to "${def.template}"`);
    } catch (err) {
        console.warn(`  ⚠  Template not set — deploy theme files first, then re-run this script.`);
        console.warn(`     Expected template: ${def.template}`);
    }

    // Set ACF fields
    if (def.acf && Object.keys(def.acf).length > 0) {
        try {
            await setAcfFields(page.id, def.acf);
            console.log(`  ✓  ACF fields set`);
        } catch (err) {
            console.warn(`  ⚠  ACF fields failed: ${err.message}`);
            console.warn('     (ACF REST API plugin may need to be installed/enabled)');
        }
    }

    return page;
}

async function verifyPage(slug, label) {
    try {
        const pages = await wpGet(`/wp/v2/pages?slug=${slug}&per_page=1`);
        if (pages.length > 0) {
            console.log(`  ✓  ${label} exists (ID: ${pages[0].id}, URL: ${pages[0].link})`);
        } else {
            console.warn(`  ⚠  ${label} not found (slug: "${slug}")`);
        }
    } catch (err) {
        console.warn(`  ⚠  Could not verify ${label}: ${err.message}`);
    }
}

async function run() {
    console.log(`\nTLTH Page Seeder — ${WP_URL}\n`);
    console.log('── Creating / Updating Pages ──────────────────────────');

    for (const def of pages) {
        try {
            const page = await upsertPage(def);
            console.log(`     URL: ${page.link}\n`);
        } catch (err) {
            console.error(`  ✗  Failed "${def.title}": ${err.message}\n`);
        }
    }

    console.log('── Verifying Existing Pages ────────────────────────────');
    await verifyPage('contact', 'Contact');
    await verifyPage('boutique', 'Boutique (or check WooCommerce shop page)');

    console.log('\nDone.\n');
}

run().catch(err => {
    console.error('Fatal error:', err);
    process.exit(1);
});
