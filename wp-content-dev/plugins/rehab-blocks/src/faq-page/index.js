import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const cats = a.categories || [];
		const setCat = ( i, key ) => ( v ) => {
			const next = cats.map( ( c, idx ) => ( idx === i ? { ...c, [ key ]: v } : c ) );
			setAttributes( { categories: next } );
		};
		// Q&A pairs edited as "Question | Answer" lines for simplicity.
		const itemsToText = ( items ) => ( items || [] ).map( ( it ) => `${ it.q } | ${ it.a }` ).join( '\n' );
		const textToItems = ( text ) => text.split( '\n' ).filter( ( l ) => l.includes( '|' ) ).map( ( l ) => {
			const idx = l.indexOf( '|' );
			return { q: l.slice( 0, idx ).trim(), a: l.slice( idx + 1 ).trim() };
		} );
		return (
			<>
				<InspectorControls>
					{ cats.map( ( c, i ) => (
						<PanelBody key={ i } title={ `Category — ${ c.label || c.id }` } initialOpen={ false }>
							<TextControl label="Anchor id" value={ c.id } onChange={ setCat( i, 'id' ) } />
							<TextControl label="Label" value={ c.label } onChange={ setCat( i, 'label' ) } />
							<TextareaControl
								label="Q&A (one per line: Question | Answer)"
								value={ itemsToText( c.items ) }
								onChange={ ( v ) => setCat( i, 'items' )( textToItems( v ) ) }
								rows={ 10 }
							/>
							<Button isDestructive variant="secondary" onClick={ () => setAttributes( { categories: cats.filter( ( _, j ) => j !== i ) } ) }>Remove category</Button>
						</PanelBody>
					) ) }
					<PanelBody title="Categories" initialOpen={ false }>
						<Button variant="primary" onClick={ () => setAttributes( { categories: [ ...cats, { id: `cat-${ cats.length + 1 }`, label: 'New category', items: [] } ] } ) }>Add category</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-faq-page rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-faq-page__layout">
								<nav className="rehab-faq-page__nav">
									<p className="rehab-faq-page__nav-label">{ a.navLabel }</p>
									<ul>{ cats.map( ( c, i ) => <li key={ i }><a className={ i === 0 ? 'on' : '' }>{ c.label }</a></li> ) }</ul>
								</nav>
								<div className="rehab-faq-page__main">
									{ cats.map( ( c, i ) => (
										<div className="rehab-faq-page__cat" key={ i }>
											<h2>{ c.label }</h2>
											<div className="rehab-faq-page__list">
												{ ( c.items || [] ).slice( 0, 3 ).map( ( it, j ) => (
													<details className="rehab-faq-page__item" key={ j }><summary><span>{ it.q }</span></summary><div className="rehab-faq-page__answer">{ it.a }</div></details>
												) ) }
												{ ( c.items || [] ).length > 3 ? <p style={ { fontStyle: 'italic', color: '#7A856A' } }>… { c.items.length - 3 } more (edit in sidebar)</p> : null }
											</div>
										</div>
									) ) }
								</div>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
