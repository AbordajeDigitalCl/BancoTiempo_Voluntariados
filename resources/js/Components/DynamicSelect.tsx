import React, { useEffect, useState } from 'react';
import axios from 'axios';

const DynamicSelect = (props) => {
    const [options, setOptions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    type Props = {
        parameter: string;
    };

    useEffect(() => {
        const fetchOptions = async () => {
            try {
                const response = await axios.post('/'+props.whereTo, {})
                    .then(function (response) {
                        setOptions(response.data);
                    });
            } catch (err) {
                // setError(err.message);
            } finally {
                setLoading(false);
            }
        };

        fetchOptions();
    }, []);

    if (loading) return <p>Loading...</p>;
    if (error) return <p>Error: {error}</p>;

    return (
        <select>
            {options.map((option) => (
                <option key={option.id} value={option.value}>
                    {option.label}
                </option>
            ))}
        </select>
    );
};

export default DynamicSelect;