import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const methods = a.methods || [];
		// Socials mirror the frontend: they come from the shared brand social
		// links (Customizer theme mods), exposed as a JS global by the plugin —
		// NOT from a per-block attribute (REH-80).
		const brandSocials = ( typeof window !== 'undefined' && window.rehabContactSocials ) || [];
		const setMethod = ( i, key ) => ( v ) => {
			const next = methods.map( ( m, idx ) => ( idx === i ? { ...m, [ key ]: v } : m ) );
			setAttributes( { methods: next } );
		};
		return (
			<>
				<InspectorControls>
					{ methods.map( ( m, i ) => (
						<PanelBody key={ i } title={ `Method ${ i + 1 } — ${ m.kick || m.icon }` } initialOpen={ false }>
							<SelectControl
								label="Icon"
								value={ m.icon }
								options={ [ 'phone', 'whatsapp', 'email' ].map( ( v ) => ( { label: v, value: v } ) ) }
								onChange={ setMethod( i, 'icon' ) }
							/>
							<TextControl label="Kick (small label)" value={ m.kick } onChange={ setMethod( i, 'kick' ) } />
							<TextControl label="Value (big line)" value={ m.value } onChange={ setMethod( i, 'value' ) } />
							<TextControl label="Link (tel:/https:/mailto:)" value={ m.href } onChange={ setMethod( i, 'href' ) } />
						</PanelBody>
					) ) }
					<PanelBody title="What happens next" initialOpen={ false }>
						<TextareaControl
							label="Items (one per line)"
							value={ ( a.nextItems || [] ).join( '\n' ) }
							onChange={ ( v ) => setAttributes( { nextItems: v.split( '\n' ).filter( ( s ) => s.trim() !== '' ) } ) }
							rows={ 4 }
						/>
					</PanelBody>
					<PanelBody title="Follow / socials" initialOpen={ false }>
						<TextControl label="Follow label" value={ a.followLabel } onChange={ set( 'followLabel' ) } />
						<p style={ { color: '#757575', fontSize: '12px', marginTop: '8px' } }>
							The social icons come from the brand&rsquo;s links (shared with the site footer). To change which networks show or their URLs, edit them under <strong>Appearance → Customize</strong>.
						</p>
					</PanelBody>
					<PanelBody title="Form" initialOpen={ false }>
						<TextControl label="Anchor id" value={ a.anchorId } onChange={ set( 'anchorId' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-contact-methods rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-contact-methods__grid">
								<div className="rehab-contact-methods__rail">
									<RichText tagName="span" className="rehab-contact-methods__eyebrow" value={ a.railEyebrow } onChange={ set( 'railEyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-contact-methods__heading" value={ a.railHeading } onChange={ set( 'railHeading' ) } placeholder="Heading…" allowedFormats={ [] } />
									<div className="rehab-contact-methods__list">
										{ methods.map( ( m, i ) => (
											<div className="rehab-contact-method" key={ i }>
												<span className="rehab-contact-method__icon">{ m.icon === 'email' ? '✉' : m.icon === 'whatsapp' ? '✆' : '☎' }</span>
												<span className="rehab-contact-method__body">
													<span className="rehab-contact-method__kick">{ m.kick }</span>
													<span className="rehab-contact-method__value">{ m.value }</span>
												</span>
												<span className="rehab-contact-method__go">→</span>
											</div>
										) ) }
									</div>
									<div className="rehab-contact-methods__next">
										<RichText tagName="h3" value={ a.nextTitle } onChange={ set( 'nextTitle' ) } placeholder="Reassurance title…" allowedFormats={ [] } />
										<ul>{ ( a.nextItems || [] ).map( ( item, i ) => <li key={ i }>✓ { item }</li> ) }</ul>
									</div>
									{ brandSocials.length > 0 && (
										<div className="rehab-contact-methods__follow">
											<p>{ a.followLabel }</p>
											<div className="rehab-contact-methods__follow-row">
												{ brandSocials.map( ( s, i ) => (
													<a key={ i } href={ s.url } aria-label={ s.label } onClick={ ( e ) => e.preventDefault() } dangerouslySetInnerHTML={ { __html: s.icon } } />
												) ) }
											</div>
										</div>
									) }
								</div>
								<aside className="rehab-contact-methods__form-card">
									<RichText tagName="span" className="rehab-contact-methods__form-eyebrow" value={ a.formEyebrow } onChange={ set( 'formEyebrow' ) } placeholder="Form eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-contact-methods__form-title" value={ a.formTitle } onChange={ set( 'formTitle' ) } placeholder="Form title…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-contact-methods__form-sub" value={ a.formSub } onChange={ set( 'formSub' ) } placeholder="Form sub…" allowedFormats={ [] } />
									<div className="rehab-contact-methods__field"><label>Name</label><input type="text" placeholder="Your name" disabled /></div>
									<div className="rehab-contact-methods__field-row">
										<div className="rehab-contact-methods__field"><label>Email</label><input type="email" placeholder="you@email.com" disabled /></div>
										<div className="rehab-contact-methods__field"><label>Country</label><input type="text" placeholder="e.g. United Kingdom" disabled /></div>
									</div>
									<div className="rehab-contact-methods__field"><label>Phone</label><input type="tel" placeholder="+44…" disabled /></div>
									<div className="rehab-contact-methods__field"><label>Message</label><textarea placeholder="Anything you'd like us to know" disabled /></div>
									<span className="rehab-btn rehab-btn--luxury rehab-btn--block">
										<RichText tagName="span" value={ a.formSubmit } onChange={ set( 'formSubmit' ) } placeholder="Submit label…" allowedFormats={ [] } />
									</span>
									<RichText tagName="p" className="rehab-contact-methods__helper" value={ a.formHelper } onChange={ set( 'formHelper' ) } placeholder="Helper…" allowedFormats={ [] } />
								</aside>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
