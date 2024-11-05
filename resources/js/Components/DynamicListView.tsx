import React, { useEffect, useState } from 'react';
import axios from 'axios';

const DynamicListView = () => {
    const [options, setOptions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchOptions = async () => {
            try {
                const response = await axios.post('/select3',{})
                .then(function (response)
                {
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
        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Contact</th>
                    <th>Country</th>
                </tr>
            </thead>
            <tbody>
                {options.map((option) => (
                <tr key={option.id}>
                    <td>{option.id}</td>
                    <td>{option.value}</td>
                    <td>{option.label}</td>
                </tr>
            ))}
            </tbody>
      </table>
    );
};

export default DynamicListView;