import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const steps = a.steps || [];

		const setStep = ( i, key ) => ( v ) => {
			const next = steps.map( ( s, idx ) => ( idx === i ? { ...s, [ key ]: v } : s ) );
			setAttributes( { steps: next } );
		};
		const setItem = ( si, ii, key ) => ( v ) => {
			const next = steps.map( ( s, idx ) => {
				if ( idx !== si ) {
					return s;
				}
				const items = ( s.items || [] ).map( ( it, j ) => ( j === ii ? { ...it, [ key ]: v } : it ) );
				return { ...s, items };
			} );
			setAttributes( { steps: next } );
		};
		const addStep = () => setAttributes( { steps: [ ...steps, { key: '', short: '', label: '', items: [] } ] } );
		const removeStep = ( i ) => setAttributes( { steps: steps.filter( ( _, j ) => j !== i ) } );
		const addItem = ( si ) => {
			const next = steps.map( ( s, idx ) => ( idx === si ? { ...s, items: [ ...( s.items || [] ), { id: '', title: '', content: '' } ] } : s ) );
			setAttributes( { steps: next } );
		};
		const removeItem = ( si, ii ) => {
			const next = steps.map( ( s, idx ) => ( idx === si ? { ...s, items: ( s.items || [] ).filter( ( _, j ) => j !== ii ) } : s ) );
			setAttributes( { steps: next } );
		};

		const blockProps = useBlockProps( { className: 'drt-recovery drt-bg-white' } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Stages" initialOpen>
						{ steps.map( ( s, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #ccc', marginBottom: '1rem', paddingBottom: '0.75rem' } }>
								<TextControl label={ `Stage ${ i + 1 } key` } value={ s.key || '' } onChange={ setStep( i, 'key' ) } help="Used for tab IDs (e.g. detox)" />
								<TextControl label="Short label" value={ s.short || '' } onChange={ setStep( i, 'short' ) } />
								<TextControl label="Full label" value={ s.label || '' } onChange={ setStep( i, 'label' ) } />
								{ ( s.items || [] ).map( ( it, j ) => (
									<div key={ j } style={ { borderLeft: '2px solid #e5e5e5', marginBottom: '0.5rem', paddingLeft: '0.5rem' } }>
										<TextControl label={ `Item ${ j + 1 } id` } value={ it.id || '' } onChange={ setItem( i, j, 'id' ) } />
										<TextControl label="Item title" value={ it.title || '' } onChange={ setItem( i, j, 'title' ) } />
										<TextareaControl label="Item content" value={ it.content || '' } onChange={ setItem( i, j, 'content' ) } rows={ 3 } />
										<Button isDestructive variant="link" onClick={ () => removeItem( i, j ) }>Remove item</Button>
									</div>
								) ) }
								<Button variant="secondary" onClick={ () => addItem( i ) }>Add item</Button>
								<div><Button isDestructive variant="link" onClick={ () => removeStep( i ) }>Remove stage</Button></div>
							</div>
						) ) }
						<Button variant="primary" onClick={ addStep }>Add stage</Button>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps } aria-label="Recovery journey programs">
					<div className="drt-container">
						<div className="drt-section-header">
							<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
							<RichText tagName="h2" className="drt-heading drt-heading--lg drt-text-balance" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body" value={ a.intro } onChange={ set( 'intro' ) } placeholder="Intro…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>
						<div className="drt-recovery__wrap">
							<nav className="drt-tabs__nav drt-tabs__nav--horizontal drt-recovery__nav" role="tablist" aria-label="Recovery stages">
								{ steps.map( ( s, i ) => (
									<button key={ i } type="button" className={ `drt-tabs__trigger${ i === 0 ? ' is-active' : '' }` }>
										<span className="drt-recovery__label-short">{ s.short }</span>
										<span className="drt-recovery__label-full">{ s.label }</span>
									</button>
								) ) }
							</nav>
							<div className="drt-recovery__panels">
								{ steps.map( ( s, i ) => (
									<div key={ i } className={ `drt-tabs__panel${ i === 0 ? ' is-active' : '' }` }>
										{ ( s.items || [] ).map( ( it, j ) => (
											<div className="drt-accordion__item" key={ j }>
												<button type="button" className="drt-accordion__trigger" aria-expanded="false">
													<span className="drt-accordion__trigger-text">{ it.title }</span>
													<span className="drt-accordion__trigger-icon" aria-hidden="true">
														<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1"><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></svg>
													</span>
												</button>
												<div className="drt-accordion__content">
													<p>{ it.content }</p>
												</div>
											</div>
										) ) }
									</div>
								) ) }
							</div>
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
