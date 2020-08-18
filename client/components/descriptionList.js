import React from 'react';

function DescriptionList({ children }) {
    return (
        <dl className="description-list">
            {Object.entries(children).map(([ title, description ]) => (
                !!description && (
                    <>
                        <dt className="description-list__key">{title}</dt>
                        <dd className="description-list__value">{description}</dd>
                    </>
                )
            ))}
        </dl>
    );
}

export default DescriptionList;
