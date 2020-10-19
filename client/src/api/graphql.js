function fetchQuery(url, query, variables) {
    return (
        fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                query,
                variables
            })
        })
            .then(response => response.json())
            .then(json => json.data)
    );
}

function gql(strings) {
    return strings.raw[0].trim().replace(/\s+/g, " ");
}

export {
    fetchQuery,
    gql
};
