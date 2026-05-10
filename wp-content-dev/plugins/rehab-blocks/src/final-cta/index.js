import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const PhoneSvg = () => (
	<svg width="20" height="20" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg>
);
const WhatsappSvg = () => (
	<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.6 6.3A8 8 0 0 0 4 12a7.8 7.8 0 0 0 1.1 4L4 20l4.1-1.1a8 8 0 0 0 9.5-12.6Zm-5.6 12.4a7.1 7.1 0 0 1-3.6-1l-.3-.1L5.7 18l.6-2.4-.2-.3a6.6 6.6 0 1 1 5.9 3.4Zm3.7-4.9c-.2 0-1.2-.6-1.4-.7s-.3 0-.5.2-.5.6-.6.7-.3.1-.5 0a5.4 5.4 0 0 1-2.7-2.4c-.2-.3 0-.5.1-.6l.4-.4.1-.3v-.3l-.7-1.7c-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3 2.5 2.5 0 0 0-.7 1.8 4.3 4.3 0 0 0 .9 2.3 9.7 9.7 0 0 0 3.7 3.3 3.5 3.5 0 0 0 1.5.4 2.4 2.4 0 0 0 1.7-1 2 2 0 0 0 .1-1.2Z"/></svg>
);
const EmailSvg = () => (
	<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 7 9 6 9-6"/></svg>
);

function Markup( { a } ) {
	return (
		<section className="rehab-final-cta" id={ a.anchorId || undefined }>
			<div className="rehab-final-cta__inner">
				<div>
					{ a.eyebrow ? <span className="rehab-final-cta__eyebrow">{ a.eyebrow }</span> : null }
					<h2 className="rehab-final-cta__heading">{ a.heading }</h2>
					{ a.lead ? <p className="rehab-final-cta__lead">{ a.lead }</p> : null }
					<div className="rehab-final-cta__contact">
						{ a.phoneText ? <a href={ a.phoneHref }><PhoneSvg />Call <strong>{ a.phoneText }</strong></a> : null }
						{ a.whatsappText ? <a href={ a.whatsappHref }><WhatsappSvg />WhatsApp <strong>{ a.whatsappText }</strong></a> : null }
						{ a.emailText ? <a href={ a.emailHref }><EmailSvg />Email <strong>{ a.emailText }</strong></a> : null }
					</div>
				</div>
				<form className="rehab-final-cta__form" onSubmit={ ( e ) => { e.preventDefault(); alert( 'Thank you — our team will be in touch.' ); } }>
					<p className="rehab-final-cta__form-title">{ a.formTitle }</p>
					<p className="rehab-final-cta__form-sub">{ a.formSub }</p>
					<input type="text" placeholder="Full name" required />
					<div className="rehab-final-cta__form-row">
						<input type="email" placeholder="E-mail" required />
						<input type="tel" placeholder="Phone (with country code)" required />
					</div>
					<input type="text" placeholder="Country" />
					<textarea placeholder="Tell us briefly what's happening (optional)" maxLength={ 180 } />
					<button type="submit" className="rehab-btn rehab-btn--luxury" style={ { width: '100%' } }>{ a.formSubmit }</button>
					<p className="rehab-final-cta__form-legal">{ a.formLegal }</p>
				</form>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const update = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Header copy" initialOpen>
						<TextControl label="Anchor ID" value={ a.anchorId } onChange={ update( 'anchorId' ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ update( 'eyebrow' ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ update( 'heading' ) } />
						<TextareaControl label="Lead" value={ a.lead } onChange={ update( 'lead' ) } />
					</PanelBody>
					<PanelBody title="Contact links" initialOpen={ false }>
						<TextControl label="Phone display" value={ a.phoneText } onChange={ update( 'phoneText' ) } />
						<TextControl label="Phone href" value={ a.phoneHref } onChange={ update( 'phoneHref' ) } />
						<TextControl label="WhatsApp display" value={ a.whatsappText } onChange={ update( 'whatsappText' ) } />
						<TextControl label="WhatsApp href" value={ a.whatsappHref } onChange={ update( 'whatsappHref' ) } />
						<TextControl label="Email display" value={ a.emailText } onChange={ update( 'emailText' ) } />
						<TextControl label="Email href" value={ a.emailHref } onChange={ update( 'emailHref' ) } />
					</PanelBody>
					<PanelBody title="Form" initialOpen={ false }>
						<TextControl label="Form title" value={ a.formTitle } onChange={ update( 'formTitle' ) } />
						<TextControl label="Form subtitle" value={ a.formSub } onChange={ update( 'formSub' ) } />
						<TextControl label="Submit label" value={ a.formSubmit } onChange={ update( 'formSubmit' ) } />
						<TextareaControl label="Legal disclaimer" value={ a.formLegal } onChange={ update( 'formLegal' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Markup a={ a } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		return <Markup a={ attributes } />;
	},
} );
