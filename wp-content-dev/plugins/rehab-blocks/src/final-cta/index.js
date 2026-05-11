import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const PhoneSvg = () => ( <svg width="20" height="20" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg> );
const WhatsappSvg = () => ( <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.6 6.3A8 8 0 0 0 4 12a7.8 7.8 0 0 0 1.1 4L4 20l4.1-1.1a8 8 0 0 0 9.5-12.6Zm-5.6 12.4a7.1 7.1 0 0 1-3.6-1l-.3-.1L5.7 18l.6-2.4-.2-.3a6.6 6.6 0 1 1 5.9 3.4Zm3.7-4.9c-.2 0-1.2-.6-1.4-.7s-.3 0-.5.2-.5.6-.6.7-.3.1-.5 0a5.4 5.4 0 0 1-2.7-2.4c-.2-.3 0-.5.1-.6l.4-.4.1-.3v-.3l-.7-1.7c-.2-.4-.3-.4-.5-.4h-.4a.8.8 0 0 0-.6.3 2.5 2.5 0 0 0-.7 1.8 4.3 4.3 0 0 0 .9 2.3 9.7 9.7 0 0 0 3.7 3.3 3.5 3.5 0 0 0 1.5.4 2.4 2.4 0 0 0 1.7-1 2 2 0 0 0 .1-1.2Z"/></svg> );
const EmailSvg = () => ( <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="m3 7 9 6 9-6"/></svg> );

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Anchor" initialOpen={ false }>
						<TextControl label="Anchor ID (for #-link)" value={ a.anchorId } onChange={ set( 'anchorId' ) } />
					</PanelBody>
					<PanelBody title="Contact link URLs" initialOpen={ false }>
						<TextControl label="Phone href" value={ a.phoneHref } onChange={ set( 'phoneHref' ) } />
						<TextControl label="WhatsApp href" value={ a.whatsappHref } onChange={ set( 'whatsappHref' ) } />
						<TextControl label="Email href" value={ a.emailHref } onChange={ set( 'emailHref' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className="rehab-final-cta" id={ a.anchorId || undefined }>
						<div className="rehab-final-cta__inner">
							<div>
								<RichText tagName="span" className="rehab-final-cta__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-final-cta__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<RichText tagName="p" className="rehab-final-cta__lead" value={ a.lead } onChange={ set( 'lead' ) } placeholder="Lead paragraph…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<div className="rehab-final-cta__contact">
									<span><PhoneSvg />Call <strong><RichText tagName="span" value={ a.phoneText } onChange={ set( 'phoneText' ) } placeholder="+66 …" allowedFormats={ [] } /></strong></span>
									<span><WhatsappSvg />WhatsApp <strong><RichText tagName="span" value={ a.whatsappText } onChange={ set( 'whatsappText' ) } placeholder="+66 …" allowedFormats={ [] } /></strong></span>
									<span><EmailSvg />Email <strong><RichText tagName="span" value={ a.emailText } onChange={ set( 'emailText' ) } placeholder="info@…" allowedFormats={ [] } /></strong></span>
								</div>
							</div>
							<div className="rehab-final-cta__form">
								<RichText tagName="p" className="rehab-final-cta__form-title" value={ a.formTitle } onChange={ set( 'formTitle' ) } placeholder="Form title" allowedFormats={ [] } />
								<RichText tagName="p" className="rehab-final-cta__form-sub" value={ a.formSub } onChange={ set( 'formSub' ) } placeholder="Form subtitle" allowedFormats={ [] } />
								<input type="text" placeholder="Full name" disabled />
								<div className="rehab-final-cta__form-row">
									<input type="email" placeholder="E-mail" disabled />
									<input type="tel" placeholder="Phone" disabled />
								</div>
								<input type="text" placeholder="Country" disabled />
								<textarea placeholder="Message (optional)" disabled />
								<span className="rehab-btn rehab-btn--luxury" style={ { width: '100%' } }>
									<RichText tagName="span" value={ a.formSubmit } onChange={ set( 'formSubmit' ) } placeholder="Submit label" allowedFormats={ [] } />
								</span>
								<RichText tagName="p" className="rehab-final-cta__form-legal" value={ a.formLegal } onChange={ set( 'formLegal' ) } placeholder="Legal disclaimer" allowedFormats={ [] } />
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		return (
			<section className="rehab-final-cta" id={ a.anchorId || undefined }>
				<div className="rehab-final-cta__inner">
					<div>
						<RichText.Content tagName="span" className="rehab-final-cta__eyebrow" value={ a.eyebrow } />
						<RichText.Content tagName="h2" className="rehab-final-cta__heading" value={ a.heading } />
						<RichText.Content tagName="p" className="rehab-final-cta__lead" value={ a.lead } />
						<div className="rehab-final-cta__contact">
							<a href={ a.phoneHref }><PhoneSvg />Call <strong><RichText.Content value={ a.phoneText } /></strong></a>
							<a href={ a.whatsappHref }><WhatsappSvg />WhatsApp <strong><RichText.Content value={ a.whatsappText } /></strong></a>
							<a href={ a.emailHref }><EmailSvg />Email <strong><RichText.Content value={ a.emailText } /></strong></a>
						</div>
					</div>
					<form className="rehab-final-cta__form" data-rehab-contact-form>
						<RichText.Content tagName="p" className="rehab-final-cta__form-title" value={ a.formTitle } />
						<RichText.Content tagName="p" className="rehab-final-cta__form-sub" value={ a.formSub } />
						{ /* Honeypot — hidden from real users, bots will fill it */ }
						<div className="rehab-final-cta__honeypot" aria-hidden="true">
							<label>Don't fill this in:<input type="text" name="_hp" tabIndex="-1" autoComplete="off" /></label>
						</div>
						<input type="text" name="name" placeholder="Full name" required autoComplete="name" />
						<div className="rehab-final-cta__form-row">
							<input type="email" name="email" placeholder="E-mail" required autoComplete="email" />
							<input type="tel" name="phone" placeholder="Phone (with country code)" required autoComplete="tel" />
						</div>
						<input type="text" name="country" placeholder="Country" autoComplete="country-name" />
						<textarea name="message" placeholder="Tell us briefly what's happening (optional)" maxLength={ 500 } />
						<button type="submit" className="rehab-btn rehab-btn--luxury" style={ { width: '100%' } }>
							<RichText.Content value={ a.formSubmit } />
						</button>
						<p className="rehab-final-cta__form-status" role="status" aria-live="polite" />
						<RichText.Content tagName="p" className="rehab-final-cta__form-legal" value={ a.formLegal } />
					</form>
				</div>
			</section>
		);
	},
} );
