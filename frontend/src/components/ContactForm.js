import React, { useState } from 'react';


const ContactPage = () => {
    const [username, setUsername] = useState('');
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [message, setMessage] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        const response = await fetch('/api/contact', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, title, description }),
        });

        const data = await response.json();
        if (response.ok) {
            setMessage('Votre demande a été envoyée avec succès !');
        } else {
            setMessage(data.error || 'Une erreur est survenue.');
        }
    };

    return (
        <div className="contact-page-container">
       <h1 className='contact'>Contactez nous</h1>
            {message && <p className="contact-page-message">{message}</p>}
            <form className="contact-page-form" onSubmit={handleSubmit}>
                <div className="contact-page-form-group">
                    <label className="contact-page-label">Nom d'utilisateur (facultatif)</label>
                    <input
                        className="contact-page-input"
                        type="text"
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                    />
                </div>
                <div className="contact-page-form-group">
                    <label className="contact-page-label">Titre de la demande</label>
                    <input
                        className="contact-page-input"
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        required
                    />
                </div>
                <div className="contact-page-form-group">
                    <label className="contact-page-label">Description</label>
                    <textarea
                        className="contact-page-textarea"
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        required
                    />
                </div>
                <button className="contact-page-button" type="submit">Envoyer</button>
            </form>
        </div>
    );
};

export default ContactPage;
