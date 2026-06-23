import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const first = a.firstName || ( a.name || '' ).split( ' ' )[ 0 ];
		return (
			<>
				<InspectorControls>
					<PanelBody title="Portrait" initialOpen>
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { photoUrl: m.url, photoAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.photoUrl ? 'Replace photo' : 'Pick photo' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Photo URL" value={ a.photoUrl } onChange={ set( 'photoUrl' ) } />
						<TextControl label="Photo alt" value={ a.photoAlt } onChange={ set( 'photoAlt' ) } />
					</PanelBody>
					<PanelBody title="Details" initialOpen={ false }>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ set( 'background' ) } />
						<TextControl label="First name (form heading)" value={ a.firstName } onChange={ set( 'firstName' ) } help="Defaults to the first word of the name." />
						<TextControl label="Quote source" value={ a.quoteSrc } onChange={ set( 'quoteSrc' ) } help="Defaults to the full name." />
						<TextControl label="Bio heading" value={ a.bioTitle } onChange={ set( 'bioTitle' ) } help="Defaults to 'About [first name]'." />
						<TextControl label="Back link URL" value={ a.backUrl } onChange={ set( 'backUrl' ) } />
						<TextareaControl
							label="Trust items (one per line)"
							help="Shown under the contact card. Leave blank for none."
							value={ ( a.trustItems || [] ).join( '\n' ) }
							onChange={ ( v ) => setAttributes( { trustItems: v.split( '\n' ).map( ( s ) => s.trim() ).filter( ( s ) => s !== '' ) } ) }
							rows={ 4 }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-team-profile rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<span className="rehab-team-profile__back">← { a.backText }</span>
							<div className="rehab-team-profile__layout">
								<div className="rehab-team-profile__main">
									<RichText tagName="span" className="rehab-team-profile__role" value={ a.role } onChange={ set( 'role' ) } placeholder="Role…" allowedFormats={ [] } />
									<RichText tagName="h1" className="rehab-team-profile__name" value={ a.name } onChange={ set( 'name' ) } placeholder="Name…" allowedFormats={ [] } />
									<div className="rehab-team-profile__portrait">
										{ a.photoUrl ? <img src={ a.photoUrl } alt={ a.photoAlt } /> : <div className="rehab-team-profile__portrait-placeholder"><span>{ a.name || 'Portrait' }</span></div> }
									</div>
									{ a.quote ? (
										<blockquote className="rehab-team-profile__quote">
											<RichText tagName="p" value={ a.quote } onChange={ set( 'quote' ) } allowedFormats={ [] } />
											<cite>{ a.quoteSrc || a.name }</cite>
										</blockquote>
									) : (
										<RichText tagName="p" className="rehab-team-profile__quote-empty" value={ a.quote } onChange={ set( 'quote' ) } placeholder="Optional pull-quote (their own words)…" allowedFormats={ [] } />
									) }
									<div className="rehab-team-profile__bio">
										<h2>{ a.bioTitle || `About ${ first }` }</h2>
										<RichText tagName="p" value={ a.bio } onChange={ set( 'bio' ) } placeholder="Bio (blank line between paragraphs)…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									</div>
								</div>
								<aside className="rehab-team-profile__rail">
									<div className="rehab-team-profile__card">
										<span className="rehab-team-profile__card-eyebrow">{ a.formEyebrow }</span>
										<p className="rehab-team-profile__card-name">{ first } is <b>part of our team</b>, talk with admissions about your care</p>
										<p className="rehab-team-profile__card-sub">{ a.formSub }</p>
										<div className="rehab-team-profile__field"><label>Your name</label><input type="text" placeholder="First name" disabled /></div>
										<div className="rehab-team-profile__field"><label>Phone</label><input type="tel" placeholder="+44…" disabled /></div>
										<div className="rehab-team-profile__field"><label>Email</label><input type="email" placeholder="you@email.com" disabled /></div>
										<span className="rehab-btn rehab-btn--luxury rehab-btn--block">{ a.formSubmit }</span>
									</div>
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
