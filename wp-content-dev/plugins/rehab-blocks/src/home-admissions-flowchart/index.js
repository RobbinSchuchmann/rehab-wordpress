import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

const stepLabel = ( step, i ) => {
	if ( step.label ) {
		return step.label;
	}
	return 'Step ' + ( step.number || i + 1 );
};

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const steps = a.steps || [];
		const setStep = ( i, key ) => ( v ) => {
			const next = steps.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { steps: next } );
		};
		const addStep = () =>
			setAttributes( {
				steps: [ ...steps, { number: '', label: '', title: '', description: '', icon: '' } ],
			} );
		const removeStep = ( i ) =>
			setAttributes( { steps: steps.filter( ( _, j ) => j !== i ) } );

		const blockProps = useBlockProps( { className: 'drt-admissions drt-bg-white drt-section' } );

		return (
			<>
				<InspectorControls>
					<PanelBody title="CTA" initialOpen={ false }>
						<TextControl label="Button text" value={ a.ctaText } onChange={ set( 'ctaText' ) } />
						<TextControl label="Button URL" value={ a.ctaUrl } onChange={ set( 'ctaUrl' ) } />
					</PanelBody>
					<PanelBody title="Steps" initialOpen>
						{ steps.map( ( s, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', marginBottom: '0.75rem', paddingBottom: '0.5rem' } }>
								<TextControl label={ `Step ${ i + 1 } number` } value={ s.number || '' } onChange={ setStep( i, 'number' ) } help="Leave blank for auto index." />
								<TextControl label={ `Step ${ i + 1 } label` } value={ s.label || '' } onChange={ setStep( i, 'label' ) } help={ `Overrides "Step N". Leave blank for default.` } />
								<TextControl label={ `Step ${ i + 1 } title` } value={ s.title || '' } onChange={ setStep( i, 'title' ) } />
								<TextareaControl label={ `Step ${ i + 1 } description` } value={ s.description || '' } onChange={ setStep( i, 'description' ) } rows={ 3 } />
								<TextareaControl label={ `Step ${ i + 1 } icon (SVG)` } value={ s.icon || '' } onChange={ setStep( i, 'icon' ) } rows={ 3 } />
								<Button isDestructive variant="link" onClick={ () => removeStep( i ) }>Remove step</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addStep }>Add step</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow (optional)…" allowedFormats={ [] } />
							<RichText tagName="h2" className="drt-heading drt-heading--lg" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body drt-text-balance" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							{ a.ctaText ? (
								<a href={ a.ctaUrl || '#' } className="drt-btn drt-btn--luxury">{ a.ctaText }</a>
							) : null }
						</div>

						<div className="drt-admissions__desktop">
							<svg className="drt-admissions__path" viewBox="0 0 1200 72" preserveAspectRatio="none" fill="none" aria-hidden="true">
								<path d="M 120 36 C 180 36, 220 12, 300 12 S 400 60, 480 60 S 560 12, 660 12 S 760 60, 840 36 S 920 12, 1020 12 C 1060 12, 1080 36, 1080 36" stroke="#BEB39E" strokeWidth="1.5" strokeOpacity="0.4" strokeLinecap="round" strokeDasharray="8 6" />
							</svg>
							<div className="drt-admissions__steps">
								{ steps.map( ( s, i ) => (
									<div className="drt-admissions__step" key={ i }>
										<div className="drt-admissions__icon" dangerouslySetInnerHTML={ { __html: s.icon || '' } } />
										<span className="drt-eyebrow">{ stepLabel( s, i ) }</span>
										<h3 className="drt-heading drt-heading--sm drt-admissions__heading">{ s.title }</h3>
										<p className="drt-body drt-admissions__text">{ s.description }</p>
									</div>
								) ) }
							</div>
						</div>

						<div className="drt-admissions__mobile">
							{ steps.map( ( s, i ) => (
								<div className="drt-admissions__step-mobile" key={ i }>
									{ i < steps.length - 1 ? <div className="drt-admissions__line" aria-hidden="true" /> : null }
									<div className="drt-admissions__icon" dangerouslySetInnerHTML={ { __html: s.icon || '' } } />
									<div className="drt-admissions__step-content">
										<span className="drt-eyebrow">{ stepLabel( s, i ) }</span>
										<h3 className="drt-heading drt-heading--sm">{ s.title }</h3>
										<p className="drt-body">{ s.description }</p>
									</div>
								</div>
							) ) }
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
