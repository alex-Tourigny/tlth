module.exports = {
    content: ["./*.php", "./**/*.php", "./src/**/*.js", "./src/**/*.scss"],
    safelist: [
        // Add any classes that might be dynamically generated
        "scroll-frames",
        "frame-container",
        "frame-image",
        "frame-loading",
        "rounded-3xl",
        "rounded-t-full",
        { pattern: /^(bg|text)-(primary|secondary|deep-blue|dark-blue|teal|muted-blue|coral|gold|sky-blue|light-blue|beige|yellow|red|rose)$/ }
    ],
    theme: {
        extend: {
            // Custom color palette - extracted from Figma design
            colors: {
                // Primary Brand Colors (from Figma)
                'deep-blue': '#10426d',      // Primary brand color - main text, header, footer
                'dark-blue': '#043158',      // Darker blue - form inputs, deeper elements
                'teal': '#48c0b6',           // Accent teal - CTAs, highlights, links
                'muted-blue': '#5d84a5',     // Muted blue - secondary text, subtle elements

                // Accent Colors (from Figma)
                'coral': '#f1665d',          // Coral/red accent - unique letter highlights
                'gold': '#ffc635',           // Gold/yellow accent - unique letter highlights  
                'light-blue': '#5695d8',       // Sky blue accent - unique letter highlights

                'beige': '#FFF9F4',
                'yellow': '#FFC048',
                'red': '#FB414D',
                'rose': '#FF938E',

                // Text Colors
                'text': {
                    primary: '#10426d',      // Main text color
                    secondary: '#5d84a5',    // Secondary text
                    dark: '#043158',         // Darker text
                    light: '#ffffff',        // Light text on dark backgrounds
                },

                // Neutral Colors
                'white': '#ffffff',
                'black': '#000000',

                // Legacy aliases (for backward compatibility)
                primary: '#10426d',          // Maps to deep-blue
                secondary: '#48c0b6',        // Maps to teal
                accent: '#48c0b6',           // Maps to teal
                neutral: '#5d84a5',          // Maps to muted-blue
                dark: '#043158',             // Maps to dark-blue

                // Keep old colors for backward compatibility during migration
                'medblue': '#1D86BC',
                'light-medblue': '#D4EDFA',
                'blue': '#00ABC0',
                'blue-grey': '#768791',
                'purple': '#7940B1',
                'green': '#5ece7c',
                'strong-red': '#DE0E18',
                'light-grey': '#C3CCD3',
                'grey': '#666A72',
                'xmas-red': '#FA404D',
                'xmas-green': '#009A7E',
                'dark-red': '#AA1A47',
                'dark-green': '#09956F',
                'game-green': '#32A37C',
                'light-purple': '#E8EEFF',
                'light-green': '#BAEECE',
                'med-grey': '#EFF2F5',
                'dark-grey': '#404A56',
            },
            // Custom font families
            fontFamily: {
                primary: ["Lexend", "sans-serif"],
                secondary: ["Lexend", "sans-serif"],
            },
            // Custom font sizes
            fontSize: {
                xs: ["0.75rem", { lineHeight: "1rem" }],
                sm: ["0.875rem", { lineHeight: "1.25rem" }],
                base: ["1rem", { lineHeight: "1.5rem" }],
                lg: ["1.125rem", { lineHeight: "1.75rem" }],
                xl: ["1.25rem", { lineHeight: "1.75rem" }],
                "2xl": ["1.5rem", { lineHeight: "2rem" }],
                "3xl": ["1.875rem", { lineHeight: "2.25rem" }],
                "4xl": ["2.25rem", { lineHeight: "2.5rem" }],
                "5xl": ["3rem", { lineHeight: "1" }],
                "6xl": ["3.75rem", { lineHeight: "1" }],
                "7xl": ["4.5rem", { lineHeight: "1" }],
                "8xl": ["6rem", { lineHeight: "1" }],
                "9xl": ["8rem", { lineHeight: "1" }],
            },
            // Custom screen sizes
            screens: {
                xs: "475px",
                sm: "640px",
                md: "768px",
                lg: "1024px",
                xl: "1280px",
                "2xl": "1536px",
                "3xl": "1920px",
            },
            // Custom border radius
            borderRadius: {
                "4xl": "2rem",
                "5xl": "2.5rem",
            },
            // Custom box shadows
            boxShadow: {
                soft: "0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)",
                medium: "0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
                hard: "0 10px 40px -10px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05)",
            },
            // Custom max-width values
            maxWidth: {
                'content': '1208px',     // Standard content width for blocks
                'content-lg': '1068px',   // Larger content width for blocks
            },
        },
    },
    plugins: [],
};
