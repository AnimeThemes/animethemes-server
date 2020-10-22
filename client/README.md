# AnimeThemes Client

## Development

1. `npm install`
2. `npm run develop`

## Deployment

1. Adjust `pathPrefix` in `gatsby-config.js` to your server environment.
2. `npm run build-prod`
3. Upload the contents of `public` to your server.

- `.htaccess` files can be placed in `static` and will be copied to the `public` folder during build.

## Configuration

You can configure certain settings by creating a `.env.development` (for development) or `.env.production` (for deployment)
in the client root directory and include the following:

```ini
; The URL to the AnimeThemes API to use.
; If not specified, "https://animethemes.dev" is used as a default.
GATSBY_API_URL = http://localhost
```

## Used technologies

- [Gatsby](https://www.gatsbyjs.com/)
- [styled-components](https://styled-components.com/)
- [SWR](https://swr.vercel.app/)
- [Font Awesome](https://fontawesome.com/)
- ...some other small packages, see the `package.json`.

### APIs

- [AniList GraphQL API](https://anilist.gitbook.io/anilist-apiv2-docs/)
  - Temporarily for fetching anime covers and synopsis.
- AnimeThemes' own API
