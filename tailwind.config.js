/** @type {import ("tailwindcss").Config} */

"use strict";

export default {

    content: [

        "./node_modules/flowbite/**/*.{js,ts}",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.ts",
        "./resources/**/*.jsx",
        "./resources/**/*.tsx",
    ],

    plugins: [

        require ("flowbite/plugin") ({}),
        require ("tailwindcss-animate"),
    ],

    darkMode: "class",

    theme: {

        fontFamily: {

            "body": [

                "Inter", 
                "ui-sans-serif", 
                "system-ui", 
                "-apple-system", 
                "system-ui", 
                "Segoe UI", 
                "Roboto", 
                "Helvetica Neue", 
                "Arial", 
                "Noto Sans", 
                "sans-serif", 
                "Apple Color Emoji", 
                "Segoe UI Emoji", 
                "Segoe UI Symbol", 
                "Noto Color Emoji",
            ],

            "sans": [

                "Inter", 
                "ui-sans-serif", 
                "system-ui", 
                "-apple-system", 
                "system-ui", 
                "Segoe UI", 
                "Roboto", 
                "Helvetica Neue", 
                "Arial", 
                "Noto Sans", 
                "sans-serif", 
                "Apple Color Emoji", 
                "Segoe UI Emoji", 
                "Segoe UI Symbol", 
                "Noto Color Emoji",
            ],
        },

        extend: {

            colors: {

                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",

                primary: {

                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                },

                secondary: {

                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))",
                },

                destructive: {

                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                },

                muted: {

                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))",
                },

                accent: {

                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))",
                },

                popover: {

                    DEFAULT: "hsl(var(--popover))",
                    foreground: "hsl(var(--popover-foreground))",
                },

                card: {

                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))",
                },
            },

            borderRadius: {

                xl: "calc(var(--radius) + 4px)",
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },

            keyframes: {

                "accordion-down": {

                    from: { height: 0, },
                    to: { height: "var(--radix-accordion-content-height)", },
                },

                "accordion-up": {

                    from: { height: "var(--radix-accordion-content-height)", },
                    to: { height: 0, },
                },

                "collapsible-down": {

                    from: { height: 0, },
                    to: { height: "var(--radix-collapsible-content-height)", },
                },

                "collapsible-up": {

                    from: { height: "var(--radix-collapsible-content-height)", },
                    to: { height: 0, },
                },
            },

            animation: {

                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
                "collapsible-down": "collapsible-down 0.2s ease-in-out",
                "collapsible-up": "collapsible-up 0.2s ease-in-out",
            },
        },
    },
};
