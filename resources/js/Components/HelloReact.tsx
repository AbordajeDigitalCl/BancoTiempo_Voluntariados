
import React from 'react';
import ReactDOM from 'react-dom';

export default function HelloReact(props) {
    return (
        <h1>Hello {props.brand} React!</h1>
    );
}

if (document.getElementById('hello-react')) {
    ReactDOM.render(<HelloReact brand/>, document.getElementById('hello-react'));
}

// const root = ReactDOM.createRoot(document.getElementById('root'));
// root.render(<HelloReact />)