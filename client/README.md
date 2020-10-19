# AnimeThemes Client

## Development

1. `npm install`
2. `npm run develop`

## Deployment

1. Adjust `pathPrefix` in `gatsby-config.js` to your server environment.
2. `npm run build-prod`
3. Upload the contents of `public` to your server.

- `.htaccess` files can be placed in `static` and will be copied to the `public` folder during build.

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
