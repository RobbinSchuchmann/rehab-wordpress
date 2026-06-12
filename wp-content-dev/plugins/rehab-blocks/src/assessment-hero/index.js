import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Links & anchor" initialOpen>
						<TextControl label="Form anchor id" value={ a.anchorId } onChange={ set( 'anchorId' ) } help="The hero CTA and other CTA bands link to #anchor." />
						<TextControl label="Primary CTA URL" value={ a.primaryUrl } onChange={ set( 'primaryUrl' ) } />
						<TextControl label="Phone href" value={ a.phoneHref } onChange={ set( 'phoneHref' ) } />
					</PanelBody>
					<PanelBody title="Rating" initialOpen={ false }>
						<ToggleControl label="Show Google rating" checked={ a.showRating } onChange={ set( 'showRating' ) } />
						<TextControl label="Score" value={ a.ratingScore } onChange={ set( 'ratingScore' ) } />
						<TextControl label="Rating text" value={ a.ratingText } onChange={ set( 'ratingText' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className="rehab-assessment-hero">
						<div className="rehab-container">
							<div className="rehab-assessment-hero__grid">
								<div className="rehab-assessment-hero__copy">
									<RichText tagName="p" className="rehab-assessment-hero__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h1" className="rehab-assessment-hero__h1" value={ a.headline } onChange={ set( 'headline' ) } placeholder="Headline…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									<RichText tagName="p" className="rehab-assessment-hero__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede paragraph…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									<div className="rehab-assessment-hero__cta-row">
										<span className="rehab-btn rehab-btn--luxury">
											<RichText tagName="span" value={ a.primaryText } onChange={ set( 'primaryText' ) } placeholder="Primary CTA…" allowedFormats={ [] } />
										</span>
										<span className="rehab-phone-link">
											<RichText tagName="u" value={ a.phoneText } onChange={ set( 'phoneText' ) } placeholder="Phone…" allowedFormats={ [] } />
										</span>
									</div>
									{ a.showRating ? (
										<div className="rehab-assessment-hero__rating">
											<span>★★★★★ <strong>{ a.ratingScore }</strong> { a.ratingText }</span>
										</div>
									) : null }
									<div className="rehab-assessment-hero__signals">
										{ [ 1, 2, 3 ].map( ( i ) => (
											<div className="rehab-assessment-hero__signal" key={ i }>
												<RichText tagName="div" className="num" value={ a[ `stat${ i }Num` ] } onChange={ set( `stat${ i }Num` ) } placeholder="N" allowedFormats={ [] } />
												<RichText tagName="div" className="lbl" value={ a[ `stat${ i }Label` ] } onChange={ set( `stat${ i }Label` ) } placeholder="Signal label…" allowedFormats={ [] } />
											</div>
										) ) }
									</div>
								</div>
								<aside className="rehab-assessment-hero__form-card">
									<RichText tagName="p" className="rehab-assessment-hero__form-eyebrow" value={ a.formEyebrow } onChange={ set( 'formEyebrow' ) } placeholder="Form eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h3" className="rehab-assessment-hero__form-title" value={ a.formTitle } onChange={ set( 'formTitle' ) } placeholder="Form title…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-assessment-hero__form-sub" value={ a.formSub } onChange={ set( 'formSub' ) } placeholder="Form subtitle…" allowedFormats={ [] } />
									<div className="rehab-assessment-hero__form-row">
										<div className="rehab-assessment-hero__field"><label>Your name</label><input type="text" placeholder="First name" disabled /></div>
										<div className="rehab-assessment-hero__field"><label>Phone</label><input type="tel" placeholder="+44…" disabled /></div>
									</div>
									<div className="rehab-assessment-hero__field"><label>Email</label><input type="email" placeholder="you@email.com" disabled /></div>
									<div className="rehab-assessment-hero__field"><label>This enquiry is for</label><select disabled><option>Myself</option></select></div>
									<span className="rehab-btn rehab-btn--luxury rehab-btn--block">
										<RichText tagName="span" value={ a.formSubmit } onChange={ set( 'formSubmit' ) } placeholder="Submit label…" allowedFormats={ [] } />
									</span>
									<RichText tagName="p" className="rehab-assessment-hero__form-phone" value={ a.formPhoneLabel } onChange={ set( 'formPhoneLabel' ) } placeholder="Phone label under submit…" allowedFormats={ [] } />
									<RichText tagName="p" className="rehab-assessment-hero__consent" value={ a.formConsent } onChange={ set( 'formConsent' ) } placeholder="Consent note…" allowedFormats={ [] } />
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
