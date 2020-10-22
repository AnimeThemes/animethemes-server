import Title from "components/text/title";
import Text from "components/text";

export default function SongTitleWithArtists({ song }) {
    return (
        <Title variant="card">
            <Text link>{song.title}</Text>
            {!!song.artists && !!song.artists.length && (
                <>
                    <Text small> by </Text>
                    {song.artists.map((artist, index) => (
                        <Text key={artist.as || artist.name} link>
                            {(artist.as || artist.name) + (index === song.artists.length - 2 ? " & " : index < song.artists.length - 1 ? ", " : "")}
                        </Text>
                    ))}
                </>
            )}
        </Title>
    );
}
